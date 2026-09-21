# deploy.ps1
# Task 14 — 部署腳本（XAMPP on Windows）
# 執行前確認 XAMPP MySQL 已啟動

param(
    [switch]$Fresh    # 加 -Fresh 可清空後重建（開發用）
)

$ErrorActionPreference = "Stop"
$ProjectDir = $PSScriptRoot

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  專案紀錄系統 — 部署腳本" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# ── Step 1：建立資料庫 ──────────────────────────────────────────────
Write-Host "[1/6] 建立資料庫 project_records ..." -ForegroundColor Yellow
$mysqlBin = "C:\xampp\mysql\bin\mysql.exe"
if (Test-Path $mysqlBin) {
    & $mysqlBin -u root -h 127.0.0.1 -e "CREATE DATABASE IF NOT EXISTS project_records CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1
    Write-Host "      完成" -ForegroundColor Green
} else {
    Write-Host "      [SKIP] MySQL 執行檔不在 $mysqlBin，請手動建立資料庫。" -ForegroundColor Red
}

# ── Step 2：Migrate ──────────────────────────────────────────────────
Write-Host "[2/6] 執行 migrations ..." -ForegroundColor Yellow
if ($Fresh) {
    php artisan migrate:fresh --seed --force
} else {
    php artisan migrate --seed --force
}
Write-Host "      完成" -ForegroundColor Green

# ── Step 3：Storage Link ─────────────────────────────────────────────
Write-Host "[3/6] 建立 storage symbolic link ..." -ForegroundColor Yellow
php artisan storage:link --force 2>&1 | Out-Null
Write-Host "      完成" -ForegroundColor Green

# ── Step 4：設定資料夾權限 ────────────────────────────────────────────
Write-Host "[4/6] 確認 storage/ 寫入權限 ..." -ForegroundColor Yellow
$storagePath = Join-Path $ProjectDir "storage"
if (Test-Path $storagePath) {
    # Windows 不需 chmod，確認目錄存在即可
    $null = New-Item -ItemType Directory -Force -Path "$storagePath\app\private\records"
    Write-Host "      storage\app\private\records 已建立" -ForegroundColor Green
}

# ── Step 5：清除快取 ─────────────────────────────────────────────────
Write-Host "[5/6] 清除快取 ..." -ForegroundColor Yellow
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
Write-Host "      完成" -ForegroundColor Green

# ── Step 6：建置前端資源 ─────────────────────────────────────────────
Write-Host "[6/6] 建置前端資源（npm run build）..." -ForegroundColor Yellow
npm run build 2>&1
Write-Host "      完成" -ForegroundColor Green

# ── 完成摘要 ────────────────────────────────────────────────────────
Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  部署完成！" -ForegroundColor Green
Write-Host ""
Write-Host "  URL : http://localhost/project/public"
Write-Host ""
Write-Host "  預設帳號（密碼均為 password123）："
Write-Host "    admin@example.com  — 全權限"
Write-Host "    editor@example.com — 可新增/修改紀錄和附件"
Write-Host "    viewer@example.com — 唯讀"
Write-Host ""
Write-Host "  Codex API Token（執行一次即可）："
Write-Host '    php artisan tinker'
Write-Host "    >>> User::where('email','admin@example.com')->first()->createToken('codex')->plainTextToken"
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""
