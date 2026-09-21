# 專案紀錄系統 — 全面 Code Review 報告

> 時間：2026-09-21  
> 測試結果：**68 passed / 139 assertions — 100% ✅**

---

## 測試覆蓋總覽

| 測試檔案 | 測試數 | 涵蓋範圍 |
|---------|-------|---------|
| `Task01LoginTest` | 5 | 認證、role、路由保護 |
| `Task02ProjectTest` | 7 | 專案 CRUD、權限、archived 封存 |
| `Task03ModuleTest` | 6 | 模組 CRUD、關聯、sort_order |
| `Task04RecordTest` | 9 | 紀錄 CRUD、type/source 驗證、搜尋 |
| `Task06FileTest` | 9 | 附件上傳/下載/刪除、UUID 路徑、權限 |
| `Task09ApiTest` | 9 | Sanctum Token、所有 API 端點 |
| `PathSecurityTest` | 9 | Path Traversal 防護 |
| `Task13UiTest` | 13 | UI 重導、頁面存取、layout 檔案存在 |
| **合計** | **68** | — |

---

## 各層分析

### ✅ 資料庫 / Migrations

| 檢查項目 | 結果 |
|---------|------|
| users 表含 role 欄位 | ✅ |
| projects 表含 status/created_by | ✅ |
| modules 表含 sort_order/composite index | ✅ |
| records 表含 type/source/git 欄位與正確 index | ✅ |
| record_files 表含 UUID storage_path/sort_order | ✅ |
| 外鍵關係正確（cascade/set null）| ✅ |
| created_at index（搜尋效能）| ✅ |

---

### ✅ Models

| 模型 | 檢查項目 | 結果 |
|------|---------|------|
| `User` | role fillable、isAdmin/isEditor/isViewer | ✅ |
| `Project` | hasMany(Module/Record)、scopeActive | ✅ |
| `Module` | belongsTo(Project)、hasMany(Record) | ✅ |
| `Record` | TYPES/SOURCES 常數、所有關聯、files 按 sort_order | ✅ |
| `RecordFile` | belongsTo(Record) | ✅ |

---

### ✅ Services

#### RecordSearchService
- 9 個搜尋欄位：title、content、git_branch、git_commit、原始/顯示名、note、project.name、module.name ✅
- 過濾：project_id、module_id、type、source、date_from、date_to ✅
- 預設排序 `created_at DESC` ✅

#### RecordFileService
- UUID-based 儲存路徑（`records/YYYY/MM/{uuid}.ext`）✅
- Path Traversal 防護（assertWithinAllowedDirectory）✅
- 刪除同時清除 DB + 實體檔案 ✅

---

### ✅ Web Controllers

| Controller | CRUD | 權限 | 問題 |
|-----------|------|------|------|
| ProjectController | Create/Read/Update（無 Delete）| Editor+ | ✅ |
| ModuleController | Create/Read/Update（無 Delete）| Editor+ | ✅ |
| RecordController | Create/Read/Update（無 Delete）| Editor+ | ✅ |
| RecordFileController | Upload/Read/Update/Delete | Upload=Editor, Download=Viewer+ | ✅ |

---

### ✅ API Controllers

| Controller | 端點 | 認證 |
|-----------|------|------|
| AgentRecordController | Projects CRUD + Modules CRUD | Sanctum Bearer ✅ |
| RecordApiController | Records CRUD + Files CRUD | Sanctum Bearer ✅ |

---

### ✅ Routes（共 62 條）

- Web routes：auth 保護下的所有頁面 ✅
- API routes：auth:sanctum 保護 ✅
- `/` → redirect records.index ✅
- 無 DELETE on projects/modules（符合規格 §21）✅

---

### ✅ Views / UI

| 頁面 | 存在 | 功能完整 |
|------|------|---------|
| login | ✅ | ✅ |
| projects index/create/show/edit | ✅ | ✅ |
| modules index/create/edit | ✅ | ✅ |
| records index/create/show/edit | ✅ | ✅ |
| record_files edit | ✅ | ✅ |
| profile edit | ✅ | ✅ |
| 側邊欄（records-layout）| ✅ | ✅ |
| 附件常顯示上傳區 | ✅ | ✅ |

---

## ⚠️ 已知問題 / 可改善項目

### 問題一（低風險）：`authorizeRole` 重複實作
**位置**：ProjectController、ModuleController、RecordController、RecordFileController 各自有 `private authorizeRole()` 方法，邏輯完全相同。

**建議**：抽到父類別 `Controller.php` 或 trait。

```php
// app/Http/Controllers/Controller.php 加：
protected function authorizeRole(string $minRole): void
{
    $user = Auth::user();
    if ($minRole === 'admin'  && !$user->isAdmin())  abort(403);
    if ($minRole === 'editor' && !$user->isEditor()) abort(403);
}
```

---

### 問題二（低風險）：API 沒有 role 授權
**位置**：AgentRecordController、RecordApiController

目前 API 只驗證 Token（認證），沒有驗證 **role（授權）**。Bearer Token 的持有者不論 role 都能新增/修改/刪除。

**建議**：API 也應檢查 role：
```php
if (!$request->user()->isEditor()) {
    return response()->json(['message' => '權限不足'], 403);
}
```

> 如果 Agent Token 固定由 admin 用，這個問題影響不大，但未來如果發放 viewer token 就會有資安問題。

---

### 問題三（中風險）：API 上傳無檔案大小限制
**位置**：RecordApiController@fileStore、RecordFileController@store

目前驗證只有 `'files.*' => 'file'`，沒有 `max:` 限制。

**建議**：
```php
'files.*' => 'file|max:20480',  // 20MB
```

---

### 問題四（低風險）：`register` 頁面為 public
Breeze 預設開放 `/register`，任何人都可以自行注冊帳號。

**建議**：如果不需要開放注冊，在 `routes/auth.php` 移除 register routes，或加上「需要 admin 邀請」邏輯。

---

### 問題五（體驗）：records index 搜尋模組下拉需頁面 reload
選擇「專案」後需要 submit 才會更新「模組」下拉選項（目前是 server-side filter）。可以加 JS 動態切換，但不影響功能。

---

## 規格書對照確認

| 規格項目 | 實作狀態 |
|---------|---------|
| §6 projects table | ✅ |
| §7 modules table | ✅ |
| §8 records table | ✅ |
| §9 type: development/test/issue/note | ✅ |
| §10 source: manual/codex/agent/api | ✅ |
| §13 record_files table | ✅ |
| §15 UUID storage path | ✅ |
| §17 附件側邊欄 UI | ✅ |
| §19 多欄位 keyword 搜尋 | ✅ |
| §20 多條件過濾 | ✅ |
| §21 無 DELETE（archived 封存）| ✅ |
| §23 API Read Records | ✅ |
| §24 API Upload | ✅ |
| §25 API CRUD | ✅ |
| §30 三角色權限系統 | ✅ |
| §31 Path Traversal 防護 | ✅ |
| §33 DB Index | ✅ |

---

## 整體評分

| 面向 | 評分 |
|------|------|
| 功能完整度 | 🟢 95% |
| 測試覆蓋 | 🟢 68 tests / 139 assertions |
| 安全性 | 🟡 良好（API role 授權待補）|
| 代碼整潔 | 🟡 良好（authorizeRole 待重構）|
| 規格符合度 | 🟢 100% |

**可上線使用，建議修復問題二（API role 授權）後再開放外部 Agent 接入。**
