$DbName = "project_records"
$User = "root"
$DumpExe = "C:\xampp\mysql\bin\mysqldump.exe"
$BackupDir = "D:\DB_Backups\$DbName"

# 確保備份目錄存在
if (!(Test-Path -Path $BackupDir)) {
    New-Item -ItemType Directory -Force -Path $BackupDir | Out-Null
}

$DateStr = Get-Date -Format "yyyyMMdd_HHmmss"
$OutputFile = "$BackupDir\${DateStr}.sql"

# 執行備份
& $DumpExe -u $User $DbName | Out-File -FilePath $OutputFile -Encoding utf8

# (可選) 刪除超過 14 天的舊備份，避免塞滿硬碟
Get-ChildItem -Path $BackupDir -Filter "*.sql" | Where-Object { $_.CreationTime -lt (Get-Date).AddDays(-14) } | Remove-Item -Force
