# 專案紀錄系統 — Agent API 參考文件

> Base URL: `http://localhost/project/public`  
> 所有 API 端點前綴：`/api`  
> 認證方式：**Sanctum Bearer Token**

---

## 認證

### 取得 Token（一次性，由管理員在伺服器執行）

```bash
php artisan tinker
>>> User::where('email','admin@example.com')->first()->createToken('agent-token')->plainTextToken
```

### 每次 Request 必須帶的 Headers

```http
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

未帶 token → `401 Unauthorized`

---

## 專案（Projects）

### `GET /api/projects` — 列出所有 active 專案

```http
GET /api/projects
```

**Response 200**
```json
[
  {
    "id": 1,
    "name": "後台重構",
    "description": "...",
    "status": "active",
    "created_by": 1,
    "created_at": "2026-09-21T10:00:00Z",
    "updated_at": "2026-09-21T10:00:00Z"
  }
]
```

---

### `POST /api/projects` — 建立專案

```json
{
  "name": "新專案名稱",
  "description": "選填說明",
  "status": "active"
}
```

| 欄位 | 必填 | 值 |
|------|------|----|
| `name` | ✅ | string, max 150 |
| `description` | ❌ | string |
| `status` | ✅ | `active` \| `archived` |

**Response 201** — 回傳新建的專案物件

---

### `GET /api/projects/{id}` — 取得專案詳細

**Response 200** — 包含 `modules` 陣列

---

### `PUT /api/projects/{id}` — 更新專案

```json
{
  "name": "更新後名稱",
  "status": "archived"
}
```

> 注意：專案不支援刪除，封存請設 `status: "archived"`

---

## 模組（Modules）

### `GET /api/projects/{project_id}/modules` — 列出專案下的模組

**Response 200**
```json
[
  {
    "id": 1,
    "project_id": 1,
    "name": "登入模組",
    "status": "active",
    "sort_order": 0
  }
]
```

---

### `POST /api/projects/{project_id}/modules` — 建立模組

```json
{
  "name": "模組名稱",
  "description": "選填說明",
  "status": "active",
  "sort_order": 0
}
```

| 欄位 | 必填 | 值 |
|------|------|----|
| `name` | ✅ | string, max 150 |
| `description` | ❌ | string |
| `status` | ✅ | `active` \| `archived` |
| `sort_order` | ❌ | integer, 預設 0 |

**Response 201**

---

### `GET /api/modules/{id}` — 取得模組詳細

---

### `PUT /api/modules/{id}` — 更新模組

```json
{
  "name": "新名稱",
  "sort_order": 1
}
```

---

## 紀錄（Records）

### `GET /api/records` — 查詢紀錄

支援多重過濾條件（全部選填）：

| 參數 | 說明 |
|------|------|
| `keyword` | 關鍵字搜尋（title, content, git_branch, git_commit, 附件名, 專案名, 模組名） |
| `project_id` | 指定專案 |
| `module_id` | 指定模組 |
| `type` | `development` \| `test` \| `issue` \| `note` |
| `source` | `manual` \| `codex` \| `agent` \| `api` |
| `date_from` | `YYYY-MM-DD` |
| `date_to` | `YYYY-MM-DD` |

**範例**
```http
GET /api/records?keyword=登入&type=issue&project_id=1
```

**Response 200** — 分頁，每頁 50 筆
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "project_id": 1,
      "module_id": null,
      "type": "issue",
      "source": "agent",
      "title": "登入 API 回傳 500",
      "content": "...",
      "git_branch": "fix/login",
      "git_commit": "a1b2c3d",
      "created_by": 1,
      "created_at": "2026-09-21T10:00:00Z",
      "project": { "id": 1, "name": "後台重構" },
      "module": null,
      "files": []
    }
  ],
  "per_page": 50,
  "total": 1
}
```

---

### `POST /api/records` — 建立紀錄

```json
{
  "project_id": 1,
  "module_id": 2,
  "type": "development",
  "title": "實作登入功能",
  "content": "完整描述...",
  "source": "agent",
  "git_branch": "feat/login",
  "git_commit": "abc1234"
}
```

| 欄位 | 必填 | 值 |
|------|------|----|
| `project_id` | ✅ | integer, 需存在 |
| `module_id` | ❌ | integer, 需存在 |
| `type` | ✅ | `development` \| `test` \| `issue` \| `note` |
| `title` | ✅ | string, max 255 |
| `content` | ✅ | string |
| `source` | ✅ | `manual` \| `codex` \| `agent` \| `api` |
| `git_branch` | ❌ | string, max 255 |
| `git_commit` | ❌ | string, max 100 |

**Response 201** — 回傳新建的紀錄物件

---

### `GET /api/records/{id}` — 取得紀錄詳細（含附件列表）

**Response 200**
```json
{
  "id": 1,
  "title": "...",
  "files": [
    {
      "id": 1,
      "original_name": "report.pdf",
      "display_name": "報告",
      "note": "...",
      "mime_type": "application/pdf",
      "extension": "pdf",
      "file_size": 102400,
      "sort_order": 0,
      "storage_path": "records/2026/09/uuid.pdf"
    }
  ]
}
```

---

### `PUT /api/records/{id}` — 更新紀錄

所有欄位皆為選填（只更新傳入的欄位）：

```json
{
  "title": "修改後標題",
  "content": "修改後內容",
  "type": "note"
}
```

---

## 附件（Record Files）

### `POST /api/records/{record_id}/files` — 上傳附件

> `Content-Type: multipart/form-data`（上傳時不能用 JSON）

```
files[0] = <binary>
files[1] = <binary>
```

**Response 201**
```json
[
  {
    "id": 5,
    "record_id": 1,
    "original_name": "screenshot.png",
    "display_name": "screenshot",
    "storage_path": "records/2026/09/uuid.png",
    "mime_type": "image/png",
    "extension": "png",
    "file_size": 20480,
    "sort_order": 0
  }
]
```

---

### `PUT /api/record-files/{id}` — 修改附件資訊

> 只能改 `display_name` / `note` / `sort_order`，`original_name` 不可改

```json
{
  "display_name": "新顯示名稱",
  "note": "這份是最終版",
  "sort_order": 1
}
```

**Response 200** — 回傳更新後的附件物件

---

### `GET /api/record-files/{id}/download` — 下載附件

**Response** — 直接回傳檔案 binary（Content-Disposition: attachment）

---

### `DELETE /api/record-files/{id}` — 刪除附件

**Response 200**
```json
{ "message": "附件已刪除。" }
```

---

## Agent 常用工作流程

### 流程一：新增開發紀錄 + 附件

```bash
# 1. 建立紀錄
POST /api/records
{
  "project_id": 1,
  "type": "development",
  "title": "完成登入 API",
  "content": "實作了 JWT + refresh token 機制...",
  "source": "agent",
  "git_branch": "feat/login",
  "git_commit": "abc1234"
}
# → 取得 record.id = 42

# 2. 上傳附件（multipart/form-data）
POST /api/records/42/files
files[0] = <screenshot.png>
```

---

### 流程二：查詢最近的 issue 紀錄

```bash
GET /api/records?type=issue&date_from=2026-09-01&keyword=登入
```

---

### 流程三：追加附件到既有紀錄

```bash
POST /api/records/{existing_id}/files
files[0] = <new_log.txt>
```

> 附件數量**無上限**，可以無限追加

---

## 錯誤格式

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "type": ["The selected type is invalid."],
    "project_id": ["The project id field is required."]
  }
}
```

| HTTP Code | 說明 |
|-----------|------|
| 200 | 成功 |
| 201 | 建立成功 |
| 401 | 未認證（缺少或無效 token） |
| 403 | 權限不足 |
| 404 | 資源不存在 |
| 422 | 驗證失敗 |
| 500 | 伺服器錯誤 |
