<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="View/Pictures/pesaanimada.jpg" type="image/x-icon">
    <title>UpeFit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            padding: 20px;
            text-align: center;
        }

        .logo-container img {
            width: 100px;
            margin-bottom: 20px;
        }

        .formtlo {
            font-size: 28px;
            font-weight: bold;
            color: #4B0082;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 1px 1px 3px rgba(75, 0, 130, 0.5);
        }

        .ub1 {
            margin: 10px 0;
            font-size: 14px;
            color: #555555;
            text-align: left;
        }

        input[type="text"], input[type="password"], select {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #cccccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input[type="text"]:focus, input[type="password"]:focus, select:focus {
            outline: none;
            border-color: #4B0082;
            box-shadow: 0 0 4px rgba(75, 0, 130, 0.4);
        }

        .boton {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #4B0082;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            margin: 10px 0;
        }

        .boton:hover {
            background-color: #372a63;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .button-container a {
            text-decoration: none;
            display: block;
            text-align: center;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        @media (max-width: 600px) {
            .button-container {
                flex-direction: column;
            }

            .boton {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="formtlo">UpeFit</div>
        
        <!-- Logo -->
        <div class="logo-container">
            <img src="View/Pictures/LogoU.png" alt="Logo de la institución">
        </div>

        <!-- Formulario de inicio de sesión -->
        <form method="post" action="Controller/login.php">
            <label for="txtmatricula" class="ub1">&#128273; Ingresa tu matrícula</label>
            <input type="text" name="txtmatricula" id="txtmatricula" placeholder="Matrícula" required>

            <label for="txtpassword" class="ub1">&#128274; Ingresa tu contraseña</label>
            <input type="password" name="txtpassword" id="txtpassword" placeholder="Contraseña" required>

            <div class="ub1">
                <input type="checkbox" id="showPassword" onclick="verpassword()"> Mostrar contraseña
            </div>

            <label for="rol" class="ub1">Rol</label>
            <select name="rol" id="rol" required>
                <option value="" disabled selected>Seleccionar</option>
                <option value="Alumno">Alumno</option>
                <option value="Admin">Administrador</option>
                <option value="Entrenador">Entrenador</option>
            </select>

            <!-- Botones de acción -->
            <div class="button-container">
                <input type="submit" value="Ingresar" class="boton">
                <a href="View/registrar.php" class="boton">Regístrate</a>
            </div>
        </form>
    </div>

    <script>
        function verpassword() {
            const passwordField = document.getElementById("txtpassword");
            passwordField.type = passwordField.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
