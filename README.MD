# MakanSiang.ga

## Cara Install
### MySQL Database
1. Buat Database terlebih dahulu. Harap simpan nama DB (1).
2. Jika tidak memiliki root, mohon buat User Database terlebih dahulu dan beri koneksi ke Database yang telah dibuat. Harap simpan nama User (2) dan Password (3).
3. Execute SQL: /install/table.sql

### G+
1. Ikuti tutorial pembuatan Google+ API ->
2. Ambila Nama G+ App. Harap Simpan (4).
3. Ambil G+ Client ID. Harap simpan (5).
4. Ambil G+ Client Secret. Harap Simpan (6).

### Settings.php
1. Copy: settings.sample.php -> settings.php
2. Buka dan ubah settings.php sesuai database kamu.
3. *$mysql_host* : Tempat kamu menyimpan database, biasanya berbentuk IP atau Domain. Default: Localhost
4. *$mysql_username* : (2) / Default: root
5. *$mysql_password* : (3) / Default: 
6. *$mysql_database* : (1) / Default: makansiang
7. *$config['gplus_app_name']* : (4) / No Default
8. *$config['gplus_client_id']* : (5) / No Default
9. *$config['gplus_client_secret']* : (6) / No Default
10. *$config['full_domain']* : URL yang kamu akan jadikan web MakanSiang / Default: http://makansiang.ga/
11. *$config['email_domain']* : Domain E-Mail G+ / Default: tokopedia.com
12. *$config['max_order_time']* : Maksimal waktu order, format = JamMenit / Default: 1030
13. &$config['max_order']* : Maksimal pesanan perhari / Default: 50

### Login Backend

Username: admin

Password: admin

