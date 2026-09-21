# 專案紀錄系統－開發規格書

版本：v1.0  
技術基線：PHP 8.1 / Laravel 10 / MySQL 8  
定位：快速上線的「專案開發／測試紀錄系統」，並提供 Codex / AI Agent API 寫入必要資訊。

---

# 1. 系統目標

本系統只解決以下核心問題：

1. 依「專案 / 模組」快速整理開發紀錄。
2. 可記錄：
   - 開發紀錄
   - 測試紀錄
   - 問題紀錄
   - 人工備註
3. 每筆紀錄可綁定任意數量附件。
4. 每個附件都有：
   - 原始檔名
   - 顯示名稱
   - 獨立備註
   - 排序
5. 可依專案、模組、類型、關鍵字快速搜尋。
6. 提供 Codex / AI Agent API：
   - 讀取既有紀錄
   - 新增開發紀錄
   - 新增測試紀錄
   - 上傳相關附件
7. 紀錄應比本地 AI 對話更穩定，可長期保存、查詢與備份。

---

# 2. 第一版範圍

第一版只做以下功能：

- 使用者登入
- 專案管理
- 模組管理
- 紀錄新增 / 修改 / 查詢
- 開發 / 測試 / 問題 / 備註四種紀錄
- 多附件上傳
- 附件顯示名稱修改
- 附件獨立備註
- 附件排序
- 關鍵字搜尋
- Codex / Agent API
- API Token 驗證
- 基本權限

---

# 3. 第一版明確不做

以下功能先不做：

- UML 編輯器
- 流程圖節點系統
- AI Memory Graph
- Vector Database
- Elasticsearch
- Agent 自動推導業務規則
- 文件版本管理
- 複雜審核流程
- 多層 File Link
- 自動分析 PDF / Word
- GitHub Webhook
- 自動 Commit 同步
- 多層模組樹

若未來出現明確需求，再追加。

---

# 4. 技術架構

## 4.1 後端

- PHP 8.1
- Laravel 10
- REST API
- Laravel Sanctum

## 4.2 資料庫

- MySQL 8
- Charset：utf8mb4

## 4.3 前端

第一版優先：

- Laravel Blade
- Bootstrap 或既有前端框架

若目前專案已有 Vue，可沿用 Vue。

第一版不因為 UI 而增加不必要架構。

## 4.4 檔案儲存

使用 Laravel Storage。

實體檔案不可直接使用顯示名稱做路徑。

建議：

```text
storage/app/private/records/{YYYY}/{MM}/{uuid}.{ext}
```

---

# 5. 系統資料模型

第一版核心 Table：

```text
users
personal_access_tokens

projects
modules
records
record_files
```

---

# 6. projects 專案表

## 6.1 用途

紀錄最上層專案。

例如：

- 林園門禁
- 台中廠
- Project Knowledge Map

## 6.2 Table

```text
projects
- id BIGINT UNSIGNED PK
- name VARCHAR(150) NOT NULL
- description TEXT NULL
- status VARCHAR(20) NOT NULL DEFAULT 'active'
- created_by BIGINT UNSIGNED NULL
- created_at TIMESTAMP
- updated_at TIMESTAMP
```

## 6.3 status

- `active（啟用）`
- `archived（封存）`

封存不代表刪除。

---

# 7. modules 模組表

## 7.1 用途

專案下的主要功能分類。

例如：

```text
林園門禁
├─ 門禁入廠
├─ 門禁離廠
├─ 工作許可證
└─ 承攬商
```

## 7.2 Table

```text
modules
- id BIGINT UNSIGNED PK
- project_id BIGINT UNSIGNED NOT NULL
- name VARCHAR(150) NOT NULL
- description TEXT NULL
- sort_order INT NOT NULL DEFAULT 0
- status VARCHAR(20) NOT NULL DEFAULT 'active'
- created_at TIMESTAMP
- updated_at TIMESTAMP
```

## 7.3 規則

第一版只做：

```text
Project → Module
```

不做無限階層。

若未來確定需要「模組 → 子模組」，再新增：

```text
parent_id
```

不得在第一版為未確認需求增加複雜度。

---

# 8. records 紀錄表

## 8.1 用途

本系統最核心 Table。

所有主要紀錄都放在此表：

- 開發紀錄
- 測試紀錄
- 問題紀錄
- 人工備註

## 8.2 Table

```text
records
- id BIGINT UNSIGNED PK
- project_id BIGINT UNSIGNED NOT NULL
- module_id BIGINT UNSIGNED NULL

- type VARCHAR(30) NOT NULL
- title VARCHAR(255) NOT NULL
- content LONGTEXT NOT NULL

- source VARCHAR(30) NOT NULL DEFAULT 'manual'

- git_branch VARCHAR(255) NULL
- git_commit VARCHAR(100) NULL

- created_by BIGINT UNSIGNED NULL
- created_at TIMESTAMP
- updated_at TIMESTAMP
```

---

# 9. records.type

第一版固定四種：

```text
development（開發紀錄）
test（測試紀錄）
issue（問題紀錄）
note（人工備註）
```

不得自行增加其他業務類型，除非需求明確要求。

---

# 10. records.source

用來辨別來源：

```text
manual（人工）
codex（Codex）
agent（其他 AI Agent）
api（其他 API）
```

---

# 11. 開發紀錄格式

建議內容包含：

```text
修改原因：
...

修改內容：
...

影響範圍：
...

涉及檔案：
...

尚待確認：
...
```

例如：

```text
標題：
P2 相容性修正

修改原因：
既有 P2 情境在門禁判斷中行為不一致。

修改內容：
調整 DoorTrait.php 相關判斷。

影響範圍：
門禁入廠流程。

涉及檔案：
app/Http/Traits/Factory/DoorTrait.php

尚待確認：
無。
```

---

# 12. 測試紀錄格式

建議內容包含：

```text
測試情境：
...

前置條件：
...

執行方式：
...

預期結果：
...

實際結果：
...

測試結論：
...
```

例如：

```text
標題：
特殊人員入廠測試

測試情境：
jobkind_original=4（特殊人員）入廠。

前置條件：
人員資料、案件資料皆存在。

執行方式：
呼叫既有門禁流程。

預期結果：
依目前確認規則判斷。

實際結果：
...

測試結論：
通過 / 失敗 / 待確認。
```

若 Expected Result 無法確認：

> 必須標記「待確認 / 實作假設」，不得自行推測。

---

# 13. record_files 附件表

## 13.1 用途

一筆紀錄可綁定 0～N 個附件。

附件數量不在資料模型上設固定上限。

實際限制由：

- PHP upload limit
- Web Server limit
- Server Storage

決定。

## 13.2 Table

```text
record_files
- id BIGINT UNSIGNED PK
- record_id BIGINT UNSIGNED NOT NULL

- original_name VARCHAR(255) NOT NULL
- display_name VARCHAR(255) NOT NULL
- note TEXT NULL

- storage_path VARCHAR(500) NOT NULL
- mime_type VARCHAR(150) NULL
- extension VARCHAR(30) NULL
- file_size BIGINT UNSIGNED NOT NULL

- sort_order INT NOT NULL DEFAULT 0

- uploaded_by BIGINT UNSIGNED NULL
- created_at TIMESTAMP
- updated_at TIMESTAMP
```

---

# 14. 附件欄位規則

## original_name（原始檔名）

例如：

```text
S__13656072.jpg
```

用途：

- 保留實際來源
- 上傳後不可由一般重新命名操作修改

## display_name（顯示名稱）

例如：

```text
林園門禁入廠流程圖
```

用途：

- UI 顯示
- 可隨時修改
- 不影響實體檔案

## note（附件備註）

例如：

```text
主管確認流程時使用，2026/09/18 更新。
```

用途：

- 說明附件內容
- 說明用途
- 補充版本或來源資訊

可隨時修改。

## sort_order（排序）

用途：

- 讓重要圖片或附件放最上面
- UI 可提供上下移動或拖曳排序

---

# 15. 實體檔案規則

不可使用：

```text
/uploads/林園門禁/門禁入廠/主管流程圖.jpg
```

作為實際儲存結構。

建議：

```text
storage/app/private/records/2026/09/{uuid}.jpg
```

資料庫保存：

```text
original_name = S__13656072.jpg
display_name = 林園門禁入廠流程圖
storage_path = records/2026/09/{uuid}.jpg
```

修改顯示名稱時，只修改 DB。

不得重新命名實體檔案。

---

# 16. UI 規格

## 16.1 首頁

```text
┌──────────────────────────────────────────────────┐
│ 搜尋： [ P2 / 工安 / 屆齡 / DoorTrait... ]      │
├───────────────┬──────────────────────────────────┤
│ 專案          │ 紀錄                             │
│               │                                  │
│ 林園門禁      │ [全部] [開發] [測試] [問題]     │
│ 台中廠        │                                  │
│               │ 09/18 P2 相容性修正              │
│ 模組          │ 09/18 特殊人員入廠測試          │
│               │ 09/17 工安問題紀錄              │
│ 門禁入廠      │                                  │
│ 門禁離廠      │                                  │
└───────────────┴──────────────────────────────────┘
```

---

# 17. 紀錄詳細頁

```text
P2 相容性修正

類型：
開發紀錄

來源：
Codex

專案：
林園門禁

模組：
門禁入廠

Branch：
develop

Commit：
abc123

內容：
...

附件：
────────────────────────────
門禁入廠流程圖
原始檔名：S__13656072.jpg
備註：主管確認流程時使用
[預覽] [修改] [下載] [刪除]

P2 測試結果
原始檔名：test_result.xlsx
備註：特殊人員測試結果
[修改] [下載] [刪除]
────────────────────────────

[新增附件]
```

---

# 18. 附件修改 UI

附件的修改功能只修改：

```text
display_name
note
sort_order
```

例如：

```text
顯示名稱：
[ 林園門禁入廠流程圖 ]

備註：
[ 主管確認流程時使用 ]

排序：
[ 1 ]
```

`original_name` 顯示但不可改。

---

# 19. 搜尋規格

第一版搜尋以下欄位：

## projects

```text
name
description
```

## modules

```text
name
description
```

## records

```text
title
content
git_branch
git_commit
```

## record_files

```text
original_name
display_name
note
```

---

# 20. 搜尋條件

API / UI 支援：

```text
project_id
module_id
type
keyword
source
date_from
date_to
```

例如：

```text
keyword = 屆齡
project_id = 1
type = test
```

可以找到：

> 林園門禁 → 門禁入廠 → 屆齡測試紀錄

---

# 21. Project API

```http
GET    /api/projects
POST   /api/projects
GET    /api/projects/{id}
PUT    /api/projects/{id}
```

第一版不提供真正 DELETE。

不使用的 Project 改：

```text
status = archived（封存）
```

---

# 22. Module API

```http
GET    /api/projects/{project}/modules
POST   /api/projects/{project}/modules
GET    /api/modules/{id}
PUT    /api/modules/{id}
```

---

# 23. Record API

## 查詢

```http
GET /api/records
```

Query：

```text
project_id
module_id
type
keyword
source
date_from
date_to
```

## 建立

```http
POST /api/records
```

Example：

```json
{
  "project_id": 1,
  "module_id": 3,
  "type": "development",
  "title": "P2 相容性修正",
  "content": "修改 DoorTrait.php 的 P2 判斷...",
  "source": "codex",
  "git_branch": "develop",
  "git_commit": "abc123"
}
```

## 修改

```http
PUT /api/records/{id}
```

## 詳細

```http
GET /api/records/{id}
```

---

# 24. Attachment API

## 多附件上傳

```http
POST /api/records/{record}/files
```

使用：

```text
multipart/form-data
files[]
```

可一次上傳多個檔案。

## 修改附件

```http
PUT /api/record-files/{id}
```

Request：

```json
{
  "display_name": "林園門禁入廠流程圖",
  "note": "主管確認流程時使用，2026/09/18 更新",
  "sort_order": 1
}
```

## 下載附件

```http
GET /api/record-files/{id}/download
```

下載前必須執行權限檢查。

## 刪除附件

```http
DELETE /api/record-files/{id}
```

刪除時：

1. 確認權限
2. 刪除 DB
3. 刪除實體檔案

---

# 25. Codex / Agent API

Codex 第一版只需要三個能力。

## 25.1 找專案 / 模組

```http
GET /api/projects
GET /api/projects/{id}/modules
```

## 25.2 查以前紀錄

```http
GET /api/records
```

例如：

```text
GET /api/records?project_id=1&module_id=3&keyword=P2
```

用途：

> Codex 開工前確認以前有沒有做過類似修改或測試。

## 25.3 寫入本次結果

```http
POST /api/records
```

Codex 完工後新增紀錄。

---

# 26. Codex 建議紀錄內容

Codex 完成開發後，只寫入有長期價值的內容：

```text
本次工作：
...

修改原因：
...

修改內容：
...

修改檔案：
...

測試：
...

結果：
...

尚待確認：
...

Branch：
...

Commit：
...
```

不要寫：

- Chain of Thought
- CLI 雜訊
- 重複對話
- 無意義 Log
- 逐行操作過程

系統保存的是：

> 工作結果與證據

不是完整聊天紀錄。

---

# 27. Codex 測試紀錄

Codex 執行測試後可新增：

```json
{
  "project_id": 1,
  "module_id": 3,
  "type": "test",
  "title": "特殊人員入廠測試",
  "content": "測試情境：...\\n實際結果：...\\n結論：通過",
  "source": "codex",
  "git_branch": "develop",
  "git_commit": "abc123"
}
```

有證據檔案時：

```http
POST /api/records/{record}/files
```

上傳：

- screenshot
- log
- json response
- SQL result
- Excel
- Markdown
- 圖片

---

# 28. Agent API 權限

使用 Laravel Sanctum Token。

Agent Token 最少權限：

```text
read-projects
read-modules
read-records
create-records
upload-record-files
```

不需要：

```text
delete-project
delete-module
manage-users
```

第一版避免給 Agent 過大權限。

---

# 29. 人工內容與 Agent 內容

`source = manual（人工）`

與：

`source = codex（Codex）`

必須保留來源。

Codex 不得因為寫新紀錄而修改舊人工紀錄。

Codex 若發現以前紀錄可能錯誤：

> 新增一筆 issue（問題紀錄）說明，不直接覆寫舊資料。

---

# 30. 權限

## Admin

可以：

- 管理 Project
- 管理 Module
- 所有 Record CRUD
- 附件管理
- Token 管理

## Editor

可以：

- 查詢
- 建立 / 修改 Record
- 上傳附件
- 修改附件名稱 / 備註

## Viewer

只能：

- 查詢
- 預覽
- 下載

## Agent

只能：

- API Read
- API Create Record
- API Upload Attachment

---

# 31. 安全要求

1. 附件不可直接放 public。
2. 所有下載經 Controller / Authorization。
3. Token 不可寫入 Git。
4. `.env` 不可提交。
5. 上傳限制 MIME / extension / size。
6. 使用 UUID 作為實體檔名。
7. 防止使用原始檔名造成 Path Traversal。
8. API 使用 Sanctum Bearer Token。
9. Validation 不可省略。

---

# 32. Validation

## Record

```text
project_id：required
module_id：nullable
type：required / in development,test,issue,note
title：required / max 255
content：required
source：required
git_branch：nullable
git_commit：nullable
```

## Record File

```text
files：required
files.*：file
display_name：nullable / max 255
note：nullable
sort_order：integer
```

實際檔案大小限制依部署環境確認，不在需求未確認前自行訂死。

---

# 33. Index

## projects

```text
INDEX(status)
INDEX(name)
```

## modules

```text
INDEX(project_id)
INDEX(status)
INDEX(project_id, sort_order)
```

## records

```text
INDEX(project_id)
INDEX(module_id)
INDEX(type)
INDEX(source)
INDEX(created_at)
INDEX(project_id, module_id, type)
```

## record_files

```text
INDEX(record_id)
INDEX(sort_order)
```

---

# 34. Model 關係

```text
Project
  hasMany Modules
  hasMany Records

Module
  belongsTo Project
  hasMany Records

Record
  belongsTo Project
  belongsTo Module
  hasMany RecordFiles

RecordFile
  belongsTo Record
```

---

# 35. Controller 建議

```text
app/Http/Controllers/
├─ ProjectController.php
├─ ModuleController.php
├─ RecordController.php
├─ RecordFileController.php
└─ Api/
   └─ AgentRecordController.php
```

不要為了方便測試把非常簡單的條件拆成大量 private helper。

---

# 36. Service 建議

第一版只在有明確價值時建立 Service。

建議：

```text
app/Services/
├─ RecordSearchService.php
└─ RecordFileService.php
```

不建議第一版大量建立：

```text
ProjectService
ModuleService
RecordFactory
RecordRepository
FileRepository
...
```

除非現有專案本身已使用此架構。

目標是快速上線、保持可讀性。

---

# 37. Migration 建議順序

```text
001 users / auth
002 projects
003 modules
004 records
005 record_files
```

若現有 Laravel 專案已有 Auth Table，沿用既有結構，不重建。

---

# 38. 第一版開發順序

## Task 01

Laravel / MySQL / Login 確認。

## Task 02

projects CRUD。

## Task 03

modules CRUD。

## Task 04

records CRUD。

## Task 05

Record Search。

## Task 06

record_files 多附件上傳。

## Task 07

附件重新命名 / 備註 / 排序。

## Task 08

附件預覽 / 下載 / 刪除。

## Task 09

Codex API Token。

## Task 10

Codex Read Records API。

## Task 11

Codex Create Record API。

## Task 12

Codex Upload Attachment API。

## Task 13

UI 整理。

## Task 14

整體測試與部署。

---

# 39. Agent 實作規則

提供本規格給 Codex / Coding Agent 時，必須遵守：

1. 每次只實作指定 Task。
2. 修改前先查看現有程式碼與 Migration。
3. 不得自行改變既有行為。
4. 不得自行新增未確認業務規則。
5. 必須假設時標記：
   - **實作假設，非已確認業務規則**
6. 禁止讀取或搜尋：
   - `node_modules`
   - `vendor`
7. 不得為方便測試破壞 Production Code 可讀性。
8. 自行建立的臨時測試檔測試後刪除，不加入 Git。
9. 必要服務異常時停止並回報。
10. 新增套件前確認 PHP 8.1 / Laravel 10 相容。
11. 不得提前實作未指定功能。

---

# 40. 每個 Task 完成回報格式

```text
本次 Task：
...

已完成：
...

修改檔案：
...

新增檔案：
...

Migration：
...

API：
...

UI：
...

測試：
...

影響既有功能：
...

待確認：
...

實作假設：
...
```

若沒有假設：

```text
本次無新增業務假設。
```

---

# 41. 驗收標準

第一版完成後，至少能完成以下完整情境：

## 情境 A：人工建立紀錄

1. 登入。
2. 選「林園門禁」。
3. 選「門禁入廠」。
4. 建立「開發紀錄」。
5. 填寫內容。
6. 上傳 5 個附件。
7. 分別修改附件顯示名稱。
8. 分別輸入附件備註。
9. 調整附件排序。
10. 儲存成功。

## 情境 B：搜尋以前測試

搜尋：

```text
屆齡
```

可快速找到：

```text
專案
模組
紀錄標題
紀錄內容
附件名稱
附件備註
```

中的相關資料。

## 情境 C：Codex 完工寫入

Codex：

1. 取得 Project。
2. 取得 Module。
3. POST 一筆 `development（開發紀錄）`。
4. POST 一筆 `test（測試紀錄）`。
5. 上傳測試證據。
6. Web UI 可立即查到。

---

# 42. MVP 完成定義

只要以下項目完成，即視為第一版可上線：

```text
✓ 專案管理
✓ 模組管理
✓ 四種紀錄
✓ 紀錄搜尋
✓ 任意多附件
✓ 附件原始檔名
✓ 附件顯示名稱
✓ 附件備註
✓ 附件排序
✓ Codex 查詢 API
✓ Codex 寫入 API
✓ Codex 附件 API
✓ API Token
✓ 基本權限
✓ 基本備份
```

---

# 43. 後續才考慮的功能

只有實際使用後出現需求才增加：

```text
子模組
UML
流程圖
文件版本
全文文件解析
AI 摘要
AI 自動分類
GitHub 整合
Vector Search
Agent Review
資料關聯圖
```

第一版不得為這些未確認需求先增加架構複雜度。

---

# 44. 核心原則

本系統不是要取代所有專案管理工具。

第一版只需要做到：

> **「我做過什麼、測過什麼、證據在哪裡，而且 Codex 能穩定幫我存進去。」**

只要這件事情做穩，系統就已經有實際價值。
