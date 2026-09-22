# 專案紀錄管理系統 - Agent API 介接規格

> Base URL: `http://localhost/project/public`  
> 所有 API 路由前綴：`/api`  
> 驗證方式：**Sanctum Bearer Token**

---

## 驗證

### 取得 Token（單次性，由系統管理員手動生成測試）：
```bash
php artisan tinker
>>> User::where('email','admin@example.com')->first()->createToken('agent-token')->plainTextToken
```

### 每次 Request 必須攜帶的 Headers

```http
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

未帶 token 則 `401 Unauthorized`

---

## 專案 (Projects)
### `GET /api/projects` — 列出所有 active 專案

**Response 200**
```json
[
  {
    "id": 1,
    "name": "專案名稱",
    "description": "...",
    "color": "#4f46e5",
    "status": "active"
  }
]
```

### `POST /api/projects` — 建立專案

| 欄位 | 必填 | 格式 |
|------|------|----|
| `name` | 是 | string, max 150 |
| `description` | 否 | string |
| `color` | 否 | string, max 10 (預設 #4f46e5) |
| `status` | 否 | `active` \| `archived` |

### `GET /api/projects/{id}` — 取得專案詳情
### `PUT /api/projects/{id}` — 更新專案

---

## 專案文件 (Project Files)
### `POST /api/projects/{project_id}/files` — 上傳專案文件
> `Content-Type: multipart/form-data`
```
files[0] = <binary>
files[1] = <binary>
```

### `GET /api/project-files/{id}/download` — 下載專案文件
### `DELETE /api/project-files/{id}` — 刪除專案文件

---

## 模組 (Modules)
### `GET /api/projects/{project_id}/modules` — 列出專案下的模組
### `POST /api/projects/{project_id}/modules` — 建立模組
### `GET /api/modules/{id}` — 取得模組詳情
### `PUT /api/modules/{id}` — 更新模組

---

## 紀錄 (Records)
### `GET /api/records` — 搜尋紀錄
可帶入多個查詢參數（全為選填）：
| 參數 | 說明 |
|------|------|
| `keyword` | 模糊比對 title, content, git_branch, git_commit, 以及 專案、模組名稱 |
| `project_id` | 指定專案 |
| `module_id` | 指定模組 |
| `type` | `development` \| `test` \| `issue` \| `note` |
| `source` | `manual` \| `codex` \| `agent` \| `api` |
| `date_from` | `YYYY-MM-DD` |
| `date_to` | `YYYY-MM-DD` |

### `POST /api/records` — 建立紀錄

| 欄位 | 必填 | 格式 |
|------|------|----|
| `project_id` | 是 | integer, 需存在 |
| `module_id` | 否 | integer, 需存在 |
| `type` | 是 | `development` \| `test` \| `issue` \| `note` |
| `title` | 是 | string, max 255 |
| `content` | 是 | string |
| `source` | 是 | `manual` \| `codex` \| `agent` \| `api` |
| `ai_tool` | 否 | string, max 50 (例如: ChatGPT, Agent) |
| `git_branch` | 否 | string, max 255 |
| `git_commit` | 否 | string, max 100 |

### `GET /api/records/{id}` — 取得紀錄詳情 (含附件)
### `PUT /api/records/{id}` — 更新紀錄

---

## 紀錄附件 (Record Files)
### `POST /api/records/{record_id}/files` — 上傳附件
### `PUT /api/record-files/{id}` — 更新附件資訊
### `GET /api/record-files/{id}/download` — 下載附件
### `DELETE /api/record-files/{id}` — 刪除附件
