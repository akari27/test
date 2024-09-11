<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Search</title>

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
        Search
    </x-slot>
    <h1>＜ 地図 ＞</h1>

    <!-- マップ表示部分 -->
    <div id="map"></div>

    <div id="ramen-list">
        <!-- ラーメン店のリストはここに動的に追加されます -->
    </div>
    <p>aiueo</p>

    <script>
        var map;
        var markers = [];
        var highlightedMarker = null;
        var userLocation;

        window.initMap = function() {
            var defaultLocation = { lat: 35.681236, lng: 139.767125 };

            var mapOptions = {
                center: defaultLocation,
                zoom: 12,
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // 全てのマーカーを追加
            @foreach ($locations as $location)
                @if(!empty($location->latitude) && !empty($location->longitude))
                    var marker = new google.maps.Marker({
                        position: { lat: {{ $location->latitude }}, lng: {{ $location->longitude }} },
                        map: map,
                        title: '{{ $location->address }}',
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
                        icon: 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                    });

                    fetch('https://like-test-f6022f288d49.herokuapp.com/distance', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            latitude: userLocation.lat,
                            longitude: userLocation.lng,
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data); // デバッグ用

                        // ラーメン店リストを生成
                        var ramenList = document.getElementById('ramen-list');
                        ramenList.innerHTML = ''; // 既存のリストをクリア
                        markers = []; // 既存のマーカーをクリア
                        data.forEach(ramen => {
                            ramen.shops.forEach(shop => {
                                // 私が追加した部分 緯度経度の変換
                                const lat = parseFloat(ramen.latitude);
                                const lng = parseFloat(ramen.longitude);
                                
                                // 評価が0の場合に「評価無し」と表示
                                var reviewText = shop.review_avg > 0 ? shop.review_avg : '評価無し';

                                var ramenItem = document.createElement('div');
                                ramenItem.classList.add('ramen-item');
                                ramenItem.innerHTML = `
                                    <h3>${shop.name || ''}</h3>
                                    <p>評価: ${reviewText}</p>
                                    <p>ジャンル: ${shop.category_name || ''}</p> 
                                    <p>営業時間: ${shop.open_time || ''} - ${shop.close_time || ''}</p>
                                    <p>料金: ¥${shop.min_price || ''} - ¥${shop.max_price || ''}</p>
                                    <p>住所: ${ramen.address || ''}</p> <!-- 住所情報 -->
                                    <p>距離: ${ramen.distance.toFixed(2)} km</p> <!-- 距離情報（小数点以下2桁） -->
                                    <a href="/shops/${shop.id}">詳細ページへ</a><br>
                                    <button onclick="highlightLocation(${ramen.latitude}, ${ramen.longitude})">位置を見る</button>
                                `;
                                ramenList.appendChild(ramenItem);

                                // マーカーを追加
                                var ramenMarker = new google.maps.Marker({
                                    position: { lat: ramen.latitude, lng: ramen.longitude },
                                    map: map,
                                    title: shop.name || 'ラーメン店'
                                });
                                markers.push(ramenMarker);
                            });
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
                icon: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png', // 強調表示用のアイコン
            });

            map.setCenter(userLocation);
            map.setZoom(15);
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?language=ja&region=JP&key={{ $api_key }}&callback=initMap" async defer></script>
</x-app-layout>
</body>
</html>
