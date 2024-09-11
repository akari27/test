<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>検索結果</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <style>
        #map {
            height: 500px;
            width: 100%;
        }
        #ramen-list {
            margin-top: 20px;
            font-family: Arial, sans-serif;
        }
        .ramen-item {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .ramen-item h3 {
            margin: 0 0 10px 0;
        }
        .ramen-item p {
            margin: 5px 0;
        }
        .highlight-marker {
            color: red; /* 強調表示用の色 */
        }
    </style>
</head>
<body>
<x-app-layout>
    <x-slot name="header">
        検索結果
    </x-slot>

    <h1>＜ 条件検索結果 ＞</h1>

    <!-- マップ表示部分 -->
    <div id="map"></div>

    <div id="ramen-list">
        <!-- ラーメン店のリストはここに動的に追加されます -->
    </div>

    <script>
        var map;
        var markers = [];
        var highlightedMarker = null;
        var userLocation = null;
        var selectedCategory = @json($categoryId); // サーバーから渡された変数
        var selectedRamenTags = @json($ramenTags); // サーバーから渡された変数

        window.initMap = function() {
            var defaultLocation = { lat: 35.681236, lng: 139.767125 };

            var mapOptions = {
                center: defaultLocation,
                zoom: 12,
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // 全てのマーカーを追加
            @foreach ($shops as $shop)
                @if(!empty($shop->location->latitude) && !empty($shop->location->longitude))
                    var marker = new google.maps.Marker({
                        position: { lat: {{ $shop->location->latitude }}, lng: {{ $shop->location->longitude }} },
                        map: map,
                        title: '{{ $shop->name }}',
                    });
                    markers.push(marker);
                @endif
            @endforeach

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };

                    map.setCenter(userLocation);
                    map.setZoom(15);

                    new google.maps.Marker({
                        position: userLocation,
                        map: map,
                        title: '現在地',
                        icon: 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                    });

                    fetch('/map-search', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            latitude: userLocation.lat,
                            longitude: userLocation.lng,
                            category: selectedCategory,
                            ramen_tags: selectedRamenTags
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data); // デバッグ用
                        var ramenList = document.getElementById('ramen-list');
                        ramenList.innerHTML = ''; // 既存のリストをクリア
                        markers = []; // 既存のマーカーをクリア
                    
                        data.forEach(shop => {
                            var ramenItem = document.createElement('div');
                            ramenItem.classList.add('ramen-item');
                            ramenItem.innerHTML = `
                                <h3>${shop.name || ''}</h3>
                                <p>評価: ${shop.review_avg || '評価無し'}</p>
                                <p>ジャンル: ${shop.category_name || ''}</p>
                                <p>営業時間: ${shop.open_time || ''} - ${shop.close_time || ''}</p>
                                <p>料金: ¥${shop.min_price || ''} - ¥${shop.max_price || ''}</p>
                                <p>住所: ${shop.address || ''}</p>
                                <p>距離: ${shop.distance.toFixed(2)} km</p>
                                <a href="/shops/${shop.id}">詳細ページへ</a><br>
                                <button onclick="highlightLocation(${shop.latitude}, ${shop.longitude})">位置を見る</button>
                            `;
                            ramenList.appendChild(ramenItem);
                    
                            var ramenMarker = new google.maps.Marker({
                                position: { lat: shop.latitude, lng: shop.longitude },
                                map: map,
                                title: shop.name || 'ラーメン店'
                            });
                            markers.push(ramenMarker);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }, function() {
                    alert('位置情報の取得に失敗しました。');
                });
            } else {
                alert('このブラウザは位置情報をサポートしていません。');
            }
        };

        function highlightLocation(lat, lng) {
            // すべてのマーカーの色を元に戻す
            markers.forEach(marker => marker.setIcon(null));

            // 強調表示用マーカーを追加
            if (highlightedMarker) {
                highlightedMarker.setMap(null); // 以前の強調表示マーカーを削除
            }
            highlightedMarker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: map,
                title: '強調表示地点',
                icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png', // 強調表示用のアイコン
            });

            map.setCenter({ lat: lat, lng: lng });
            map.setZoom(15);
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?language=ja&region=JP&key={{ $api_key }}&callback=initMap" async defer></script>
</x-app-layout>
</body>
</html>
