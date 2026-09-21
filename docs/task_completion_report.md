# 專案紀錄系統 — Task 完成回報

## 🏁 測試結果

```
Tests: 46 passed (94 assertions) — ✅ ALL PASS
Duration: 1.43s
```

---

## Task 01 — Laravel / MySQL / Login 確認

**已完成：**
- Laravel 10 + Breeze (Blade) 安裝完成
- MySQL DB: `project_records` (需 XAMPP MySQL 啟動後 migrate)
- SQLite in-memory 測試環境設定完成
- 新增 `role` 欄位到 `users` table (admin / editor / viewer)

**修改檔案：**
- `.env` — APP_NAME, APP_URL, DB_DATABASE
- `phpunit.xml` — SQLite :memory: + APP_URL=http://localhost
- `database/migrations/2014_10_12_000000_create_users_table.php` — 加 role

**新增檔案：**
- `tests/Unit/Task01LoginTest.php` (5 tests ✅)

---

## Task 02 — Projects CRUD

**已完成：**
- Project CRUD（無 DELETE，用 archived 封存）
- Admin/Editor 才能建立/修改
- Viewer 可查詢

**新增檔案：**
- `app/Models/Project.php`
- `app/Http/Controllers/ProjectController.php`
- `database/migrations/2026_09_18_000002_create_projects_table.php`
- `database/factories/ProjectFactory.php`
- `resources/views/projects/index|create|show|edit.blade.php`
- `tests/Unit/Task02ProjectTest.php` (7 tests ✅)

---

## Task 03 — Modules CRUD

**已完成：**
- Module 巢狀在 Project 下
- sort_order 欄位，Project→Modules 排序正確

**新增檔案：**
- `app/Models/Module.php`
- `app/Http/Controllers/ModuleController.php`
- `database/migrations/2026_09_18_000003_create_modules_table.php`
- `database/factories/ModuleFactory.php`
- `resources/views/modules/index|create|edit.blade.php`
- `tests/Unit/Task03ModuleTest.php` (6 tests ✅)

---

## Task 04 — Records CRUD

**已完成：**
- 四種 type: development / test / issue / note
- 四種 source: manual / codex / agent / api
- Record::TYPES, Record::SOURCES 常數

**新增檔案：**
- `app/Models/Record.php`
- `app/Http/Controllers/RecordController.php`
- `database/migrations/2026_09_18_000004_create_records_table.php`
- `database/factories/RecordFactory.php`
- `resources/views/records/index|create|show|edit.blade.php`
- `tests/Unit/Task04RecordTest.php` (9 tests 含 Task 05 ✅)

---

## Task 05 — Record Search

**已完成：**
- keyword 搜尋：title, content, git_branch, git_commit, original_name, display_name, note, project.name, module.name
- filter: project_id, module_id, type, source, date_from, date_to

**新增檔案：**
- `app/Services/RecordSearchService.php`

---

## Task 06 — record_files 多附件上傳

**已完成：**
- 多檔案同時上傳
- UUID-based 儲存路徑 `records/YYYY/MM/{uuid}.ext`
- original_name 保存，不用於路徑

---

## Task 07 — 附件重新命名 / 備註 / 排序

**已完成：**
- 只修改 display_name / note / sort_order
- original_name 顯示但不可修改

---

## Task 08 — 附件預覽 / 下載 / 刪除

**已完成：**
- 下載透過 Controller（不直接 public）
- 刪除同時清除 DB + 實體檔案

**新增檔案：**
- `app/Models/RecordFile.php`
- `app/Http/Controllers/RecordFileController.php`
- `app/Services/RecordFileService.php`
- `database/migrations/2026_09_18_000005_create_record_files_table.php`
- `database/factories/RecordFileFactory.php`
- `resources/views/record_files/edit.blade.php`
- `tests/Unit/Task06FileTest.php` (9 tests ✅)

---

## Task 09 — Codex API Token

**已完成：**
- Laravel Sanctum Bearer Token 驗證
- 無 token → 401

---

## Task 10 — Codex Read Records API

**已完成：**
- `GET /api/records` with filters
- `GET /api/projects` / `GET /api/projects/{id}/modules`

---

## Task 11 — Codex Create Record API

**已完成：**
- `POST /api/records` with full validation
- `POST /api/projects`, `POST /api/projects/{project}/modules`

---

## Task 12 — Codex Upload Attachment API

**已完成：**
- `POST /api/records/{record}/files`
- `PUT /api/record-files/{id}`, `DELETE /api/record-files/{id}`
- `GET /api/record-files/{id}/download`

**新增檔案：**
- `app/Http/Controllers/Api/AgentRecordController.php`
- `app/Http/Controllers/Api/RecordApiController.php`
- `routes/api.php`
- `routes/web.php`
- `tests/Unit/Task09ApiTest.php` (9 tests ✅)

---

## Migration 順序

```
001  users (+ role field)
002  projects
003  modules
004  records
005  record_files
```

---

## API 端點

| Method | URI | 說明 |
|--------|-----|------|
| GET | /api/projects | 列出所有 active 專案 |
| POST | /api/projects | 建立專案 |
| GET | /api/projects/{id} | 專案詳細 |
| PUT | /api/projects/{id} | 更新專案 |
| GET | /api/projects/{project}/modules | 模組列表 |
| POST | /api/projects/{project}/modules | 建立模組 |
| GET | /api/modules/{id} | 模組詳細 |
| PUT | /api/modules/{id} | 更新模組 |
| GET | /api/records | 查詢紀錄（含搜尋） |
| POST | /api/records | 建立紀錄 |
| GET | /api/records/{id} | 紀錄詳細 |
| PUT | /api/records/{id} | 更新紀錄 |
| POST | /api/records/{record}/files | 上傳附件 |
| PUT | /api/record-files/{id} | 修改附件資訊 |
| GET | /api/record-files/{id}/download | 下載附件 |
| DELETE | /api/record-files/{id} | 刪除附件 |

---

## ⚠️ 待操作（需要您執行）

1. **啟動 XAMPP MySQL**
2. 執行 migrate:
   ```bash
   php artisan migrate --seed
   ```
3. 登入帳號（seed 建立）：
   - `admin@example.com` / `password123`（Admin 全權限）
   - `editor@example.com` / `password123`（Editor）
   - `viewer@example.com` / `password123`（Viewer）
4. 建立 Codex API Token：
   ```bash
   php artisan tinker
   # >>> User::where('email','admin@example.com')->first()->createToken('codex-token')->plainTextToken
   ```

---

## 實作假設

- `admin` role 擁有所有 `editor` 能力（符合規格§30）
- `viewer` 可下載附件（規格§30 Viewer：查詢/預覽/下載）
- 附件 display_name 在 update 時若傳入 null 則不更新（保留原值）
- Sanctum 第一版沒有細粒度 ability scope（Token 僅做認證，role 做授權）

**實作假設，非已確認業務規則。**
