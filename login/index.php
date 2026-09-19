<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Serconslimp</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../public/templeates/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../public/templeates/AdminLTE-3.2.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../public/templeates/AdminLTE-3.2.0/dist/css/adminlte.min.css">

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="hold-transition login-page">

<div class="login-box">

    <?php
    if (isset($_SESSION['mensaje'])) {
        $respuesta = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']); // ← Limpia mensaje para que no se repita
        ?>
        <script>
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "<?php echo $respuesta; ?>",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    <?php } ?>

    <div class="card card-outline card-primary">
        <div class="card-header text-center"></div>

        <center>
            <img src="../public/images/LOGO.png" alt="" width="300px">
        </center>

        <div class="card-body">
            <p class="login-box-msg">Ingrese sus datos</p>

            <form action="../app/controllers/login/ingreso.php" method="post">
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="password_user" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Acceder</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="../public/templeates/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
<script src="../public/templeates/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../public/templeates/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>
