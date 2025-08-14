# Mikrotik Absensi
Sistem absensi otomatis berbasis web yang terintegrasi dengan MikroTik Hotspot. Karyawan akan otomatis tercatat hadir saat login ke WiFi kantor, dan logout saat disconnect. Data kehadiran tersimpan di MySQL dan dapat dikelola melalui web.


## Fitur
*  Integrasi dengan MikroTik Hotspot untuk absensi otomatis.
*  Login dan Logout absensi tanpa input manual.
*  Login dan Logout absensi tanpa input manual.
*  Export laporan ke PDF.
*  Auto logout jika perangkat tidak terdeteksi.

//note : masih dalam tahap pengembangan//


# Tech Stack
*  Backend: PHP (Native)
*  Database: MySQL
*  Frontend: HTML, CSS, JavaScript
*  Router: MikroTik (Hotspot + Script API)
*  Server OS: (Jika ada Linux/Windows disebutkan)

# Flow Sistem
<pre>
<img width="1536" height="1024" alt="flow system" src="https://github.com/user-attachments/assets/eb1043bd-a84d-406b-a94d-633a3c31746e" />
</pre>
  
# Instalasi
*git clone https://github.com/username/phire-absensi.git
*cd phire-absensi
*import database dari folder /db/phire_absensi.sql ke MySQL
*update konfigurasi koneksi database di config.php

# Integrasi Mikrotik
*/tool fetch url="http://[IP-SERVER]/submit.php" http-method=post http-data="username=$user&mac=$mac"
*/tool fetch url="http://[IP-SERVER]/logout5.php?username=$user&mac=$mac"

# Demo
<pre>
Mikrotik Jaringan
<img width="660" height="479" alt="image" src="https://github.com/user-attachments/assets/20aef72c-ce55-402f-a31a-a1a030e069aa" />
Log In 
<img width="1015" height="628" alt="image" src="https://github.com/user-attachments/assets/e895e27a-9aa0-4235-9d37-766535932152" />
Dashboard Admin
<img width="1347" height="640" alt="image" src="https://github.com/user-attachments/assets/94cce2b9-c5d2-4c1f-b12b-1581460cccc0" />
Dashboard User
<img width="1342" height="617" alt="image" src="https://github.com/user-attachments/assets/c524bf9e-e409-4b31-b7f1-e6735d9e8592" />
</pre>

