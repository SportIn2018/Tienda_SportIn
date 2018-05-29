# Tienda_SportIn
Se requieren los siguientes paquetes:\
postgresql\
php7.0\
php7.0-pgsql\
git\
php-mail\
php-mail-mime
# Instrucciones de instalación
Entrar en terminal con permisos de administración.\
Clonar el repositorio desde el directorio /var/www/ con el comando:\
\
`git clone https://github.com/SportIn2018/Tienda_SportIn.git`\
\
Ir al directorio Tienda_SportIn/instalación y ejecutar los siguientes comandos:\
\
`cp sportin.conf /etc/apache2/sites-available/`\
`cp sportin.crt /etc/ssl/certs/`\
`cp sportin.key /etc/ssl/private/`\
`a2ensite sportin`\
`a2enmod ssl`\
`service apache2 restart`\
`su postgres`\
`psql < tienda.sql`\
`exit`\
\
Nota: Por seguridad una vez terminada la instalacion del sitio mueva la carpeta de "Tienda_SportIn/instalacion" a un lugar seguro fuera del directorio "/var/www/"\
\
Para habilitar el envio de correos agregar la direccion de correo (en la variable $mail_tienda ubicada en la linea 16) y contraseña (en la variable $pass_mail_tienda ubicada en la linea 17) de esta en el archivo correo.php, ejemplo:\
\
`$mail_tienda = "correo@gmail.com"`\
`$pass_mail_tienda = "contraseña"`\
\
Nota: El envio de correos esta configurado para utilizar una cuenta de gmail, si se desea usar otro servicio se tendra que cambiar la informacion correspondiente en el archivo de correo.php\
\
Dependiendo de la configuracion de postgres en el servidor puede ser necesario agregar el usuario y base de datos al archivo de configuracion pg_hba.conf en la seccion correspondiente a IPv4, ejemplo:\
\
`host    tienda    tiendaadmin   127.0.0.1/32    md5`\
\
Una vez ejecutados todos los comandos anteriores agregar la siguiente línea al archivo /etc/hosts:\
\
`127.0.0.1 sportin.mx`\
\
La primera vez que abra el sitio debera iniciar sesion con el usuario administrador por defecto:\
\
`Login: admin`\
`Password: hola123`\
\
Es recomendable que cambie la contraseña del usuario, con este usuario podra dar privilegios a los demas usuarios registrados al sitio