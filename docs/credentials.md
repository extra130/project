# 專案紀錄系統 — 系統帳號清單

以下為系統預設建立的測試帳號（於資料庫 Seed 階段產生），所有密碼預設皆為 `password123`：

| 角色 (Role) | 姓名 | 信箱 (Email) | 密碼 (Password) | 權限說明 |
| :--- | :--- | :--- | :--- | :--- |
| **管理員 (Admin)** | Admin | `admin@example.com` | `password123` | 最高權限，包含所有 Editor 的功能。 |
| **編輯者 (Editor)** | Editor | `editor@example.com` | `password123` | 可新增/編輯/封存專案與模組，新增/編輯紀錄，上傳與刪除附件。 |
| **檢視者 (Viewer)** | Viewer | `viewer@example.com` | `password123` | 僅具備讀取權限，可瀏覽所有專案與紀錄，可下載附件（無法新增或修改任何資料）。 |

---

### 💡 備註
1. **API 存取 (Agent / Codex 使用)**：
   若需產生 API Bearer Token，請以 Admin 帳號透過 Tinker 產生（詳見 API 參考文件）。
2. **註冊功能已關閉**：
   依據稍早的安全設定，系統已關閉前台對外公開註冊功能，若需新增使用者，需由開發者透過資料庫 (Tinker / Seeder) 手動建立。
