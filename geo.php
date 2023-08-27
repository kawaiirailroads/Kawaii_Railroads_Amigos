<script>
  'use strict'

  // ログインユーザーのIDをJavaScript変数に埋め込む
  const loggedInUserIdJs = <?php echo json_encode($loggedInUserId); ?>;

  // 最寄りの漫画ページのIDを格納する変数
  let nearestMangaPageId;

  const success = pos => {
    // 現在地の緯度・経度を取得
    const latPre = pos.coords.latitude;
    const lngPre = pos.coords.longitude;

    const earthRadius = 6378.137; // 地球の半径(km)
    const latDistance = 6 / earthRadius; // 半径50mの距離に相当する緯度差
    const lngDistance = 6 / (earthRadius * Math.cos(Math.PI * latPre / 180)); // 半径50mの距離に相当する経度差

    const latMin = latPre - latDistance;
    const latMax = latPre + latDistance;
    const lngMin = lngPre - lngDistance;
    const lngMax = lngPre + lngDistance;

    // Fetch関数を使ってjsonファイルを取得し、配列に変換して処理を行う
    fetch('mangapages_min.json')
      .then(response => response.json())
      .then(data => {
        // データを受け取った後の処理はここに記述する
        const mangaPages = data;

        // 配列から緯度と経度の配列を取得
        const latitudes = mangaPages.map(page => page.lat);
        const longitudes = mangaPages.map(page => page.lng);
        const mangaInfo = mangaPages.map(page => ({
          title: page.title,
          manga: page.manga,
          caption: page.caption
        }));

        // 現在地と一番近いポイントを探す処理
        let nearest = '';
        let nearestDistance = Infinity;
        let nearlyimg;
        for (let i = 0; i < latitudes.length; i++) {
          if (latitudes[i] >= latMin && latitudes[i] <= latMax &&
            longitudes[i] >= lngMin && longitudes[i] <= lngMax) {
            const nearestMangaPageId = mangaPages[i].id;
            const targetDiv = document.getElementById('xxx');
            const mangaTitle = mangaInfo[i].title;
            const mangaImages = mangaInfo[i].manga.map(image => `<img src="${image}">`).join('<br>');
            const mangaCaption = mangaInfo[i].caption;
            targetDiv.innerHTML = `<h1>${mangaTitle}</h1><br>${mangaImages}<br>${mangaCaption}`;

            //データをPHPに送信する
            const data_to_php = {
              member_id: loggedInUserIdJs,
              manga_index: nearestMangaPageId
              };

            // データをJSON形式でPOSTリクエストとして送信
            console.log(data_to_php);
            fetch('save_to_database.php', {
              method: 'POST',
              headers: {
               'Content-Type': 'application/json'
                },
              body: JSON.stringify(data_to_php)
            })
            .then(response => response.json())
            .then(result => {
            // メッセージの表示先のdivを取得
            const saveResultDiv = document.getElementById('saveResult');

            // レスポンスのstatusに応じてメッセージを設定
            if (result.status === 'success') {
             saveResultDiv.textContent = 'ギャラリーに漫画が追加されたよ！';
            } else if (result.status === 'error') {
              if (result.message === '既にデータが存在するため、保存を中止しました') {
                saveResultDiv.textContent = 'おかえり！ 読んだことのある漫画だよ';
             } else {
                saveResultDiv.textContent = 'データベースへの保存に失敗しました';
             }
            }
            console.log(result);
            })
            .catch(error => {
            console.error('エラー！:', error);
            });
            break;
          } else {
            const distance = getDistanceFromLatLon(latPre, lngPre, latitudes[i], longitudes[i]);
            if (distance < nearestDistance) {
              nearestDistance = distance;
              nearest = mangaPages[i].name; // ヒントとして表示する名前を取得
              nearlyimg = mangaPages[i].nearly; //ヒントとして表示する画像を取得
            }
            document.getElementById('xxx').innerHTML = `ここで読める漫画はないみたい！<br>でも……${nearest}に行ったら何かあるかも!?<br><img src="${nearlyimg}">`;
          }
        }
      })
      .catch(error => {
        // エラー処理
        console.error('エラーが発生しました:', error);
      });

// 2点間の距離を求める関数
function getDistanceFromLatLon(lat1, lon1, lat2, lon2) {
  const deg2rad = deg => deg * (Math.PI / 180);
  const R = 6371; // Radius of the earth in km
  const dLat = deg2rad(lat2 - lat1);
  const dLon = deg2rad(lon2 - lon1);
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  const d = R * c; // Distance in km
  return d;
}
}

const fail = error => {
  alert(`位置情報の取得に失敗しました。エラーコード：${error.code}`);
}

navigator.geolocation.getCurrentPosition(success, fail);

</script>