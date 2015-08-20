<style>
	body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
}

.form-signin {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form-signin .form-signin-heading,
.form-signin .checkbox {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  font-size: 16px;
  height: auto;
  padding: 10px;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="text"] {
  margin-bottom: -1px;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>

<form class="form-signin" method="post" action="">
	<div class="container">
      <form class="form-signin">
        <h2 class="form-signin-heading"><?php echo LANG_LOG_IN; ?></h2>
        <input type="text" name="login" class="form-control" placeholder="<?php echo LANG_LOGIN; ?>" autofocus>
        <input type="password" name="mdp" class="form-control" placeholder="<?php echo LANG_PASSWORD; ?>">
        <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo LANG_CONNECTION; ?></button>
      </form>

    </div> <!-- /container -->
</form>