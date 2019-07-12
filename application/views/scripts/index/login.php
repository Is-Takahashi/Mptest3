<html>

<body>
  <h1>ログインページ</h1>
  <h3>ログインIDとパスワードを入力してください</h3>
<!--formに入力したidとpassの値をindexControllerのloginActionに送信する-->
  <form action="/Mptest3/public/index/login" method="post" name="loginForm">
    <table>
      <thead></thead><tfoot></tfoot>
      <tbody>
        <tr>
          <th>ログインID</th>
          <td>
            <input type="text" name="id">
          </td>
        </tr>
        <tr>
          <th>パスワード</th>
          <td>
            <input type="password" name="pass">
          </td>
        </tr>
      </tbody>
    </table>
    <input type="submit" value="ログイン">
  </form>
</body>
</html>