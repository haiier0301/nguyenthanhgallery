# How to Use the CMS

## Hướng Dẫn Sử Dụng CMS (Vietnamese)

### Đăng Nhập

1. Mở trình duyệt và truy cập: `http://your-website.com/cms/`
2. Nhập thông tin đăng nhập:
   - **Tên đăng nhập:** `admin`
   - **Mật khẩu:** `admin123`
3. Nhấn **LOGIN**

⚠️ **Quan trọng:** Đổi mật khẩu mặc định sau khi đăng nhập lần đầu!

---

## 1. Quản Lý Hoạ Sĩ (Artists)

### Thêm Hoạ Sĩ Mới

1. Vào trang **Artists** từ menu bên trái
2. Nhấn nút **+ Add New Artist**
3. Điền thông tin:
   - **Artist Name:** Tên hoạ sĩ (tiếng Việt có dấu OK)
   - **Display Name:** TÊN CHỮ HOA (hiển thị trên website)
   - **Artist Code:** Mã hoạ sĩ viết tắt (VD: NT cho Nguyen Thanh)
   - **Birth Date:** Ngày sinh (chọn từ calendar)
   - **Birth Place:** Nơi sinh (VD: Hanoi, Vietnam)
   - **Biography:** Tiểu sử hoạ sĩ (có thể dùng HTML)
   - **Featured Image:** Đường dẫn ảnh chính
   - **Thumbnail:** Đường dẫn ảnh thu nhỏ
   - **Featured Artist:** ✓ nếu là hoạ sĩ nổi bật
   - **Has Series Pages:** ✓ nếu có các trang series riêng

4. Nhấn **Save Artist**

### Sửa Thông Tin Hoạ Sĩ

1. Tìm hoạ sĩ cần sửa trong bảng
2. Nhấn nút **Edit**
3. Chỉnh sửa thông tin
4. Nhấn **Save Artist**

### Xoá Hoạ Sĩ

1. Tìm hoạ sĩ cần xoá
2. Nhấn nút **Delete**
3. Xác nhận xoá

---

## 2. Quản Lý Tác Phẩm (Artworks)

### Thêm Tác Phẩm Mới

1. Vào trang **Artworks**
2. Nhấn **+ Add New Artwork**
3. Điền thông tin:
   - **Artist:** Chọn hoạ sĩ từ dropdown
   - **Artwork Code:** Mã tác phẩm (VD: NT1, NT2, NT3...)
   - **Title:** Tên tác phẩm (có thể bỏ trống)
   - **Medium:** Chất liệu (VD: oil on canvas, acrylic on canvas)
   - **Size:** Kích thước (VD: 100x120cm)
   - **Year:** Năm sáng tác
   - **Series Year:** Năm series (chỉ cho Nguyen Thanh)
   - **Image Path:** Đường dẫn đến file ảnh
   - **Available:** ✓ nếu tác phẩm còn available

4. Nhấn **Save Artwork**

### Lọc Tác Phẩm

- **Theo Hoạ Sĩ:** Chọn tên hoạ sĩ ở dropdown "All Artists"
- **Theo Năm:** Chọn năm ở dropdown "All Years"

### Quy Tắc Đặt Mã Tác Phẩm

- **Format:** `[MÃ_HOẠ_SĨ][SỐ]`
- **Ví dụ:**
  - Nguyen Thanh (NT): NT1, NT2, NT3, NT4...
  - Ngo Dang Hiep (NDH): NDH1, NDH2, NDH3...
  - Nguyen Trang (NTR): NTR1, NTR2, NTR3...

---

## 3. Quản Lý Triển Lãm (Exhibitions)

### Thêm Triển Lãm

1. Vào trang **Exhibitions**
2. Nhấn **+ Add Exhibition**
3. Điền thông tin:
   - **Year:** Năm tổ chức
   - **Type:** Loại (Solo/Group/Award/Art Fair)
   - **Title:** Tên triển lãm
   - **Location:** Địa điểm
   - **Description:** Mô tả chi tiết

4. Nhấn **Save Exhibition**

### Các Loại Triển Lãm

- 🔵 **Solo:** Triển lãm cá nhân
- 🟢 **Group:** Triển lãm nhóm
- 🟠 **Award:** Giải thưởng / Vinh danh
- 🟣 **Art Fair:** Hội chợ nghệ thuật

---

## 4. Thư Viện Media (Media Library)

### Upload Hình Ảnh

**Cách 1: Kéo Thả (Drag & Drop)**

1. Vào trang **Media Library**
2. Kéo file ảnh vào vùng upload
3. Đợi upload hoàn tất
4. Nhấn vào ảnh để copy đường dẫn

**Cách 2: Chọn File**

1. Nhấn vào vùng upload
2. Chọn file từ máy tính
3. Confirm upload

### Copy Đường Dẫn Ảnh

1. Nhấn vào ảnh trong thư viện
2. Nhấn nút **Copy Path**
3. Paste vào form Artist hoặc Artwork

### Lọc Media

- Chọn folder từ dropdown để lọc theo loại

---

## 5. Settings (Cài Đặt)

### Đổi Mật Khẩu

1. Vào **Settings** → **Change Password**
2. Nhập mật khẩu hiện tại
3. Nhập mật khẩu mới (tối thiểu 6 ký tự)
4. Xác nhận mật khẩu mới
5. Nhấn **Update Password**

### Backup Dữ Liệu

1. Vào **Settings** → **Data Management**
2. Nhấn **Export All Data**
3. File JSON sẽ được download
4. Lưu file này an toàn

### Restore Dữ Liệu

1. Vào **Settings** → **Data Management**
2. Nhấn **Import Data**
3. Chọn file JSON đã backup trước đó
4. Xác nhận import

---

## 6. Tích Hợp CMS Với Website

### Cách 1: Tự Động (Auto-load)

Thêm vào file HTML của bạn:

```html
<script src="cms/integration.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    initCMSIntegration(); // Tự động load dựa trên trang hiện tại
  });
</script>
```

### Cách 2: Thủ Công (Manual)

**Cho trang Artists (danh sách):**

```html
<script src="cms/integration.js"></script>
<script>
  loadArtistsFromCMS(); // Load tất cả hoạ sĩ
</script>
```

**Cho trang Artist (chi tiết):**

```html
<script src="cms/integration.js"></script>
<script>
  loadArtistProfileFromCMS('nguyen-thanh'); // Load profile
  loadArtworksByCMS('nguyen-thanh', '2002'); // Load artworks của series 2002
</script>
```

**Cho trang Exhibitions:**

```html
<script src="cms/integration.js"></script>
<script>
  loadExhibitionsFromCMS(); // Load tất cả
  // Hoặc
  loadExhibitionsFromCMS('award'); // Chỉ awards
</script>
```

### Cách 3: Tạo Pages Tự Động (PHP Generator)

1. Truy cập: `cms/api/generate-pages.php?action=generate-all`
2. Tất cả trang artist sẽ được tạo tự động từ JSON
3. Các trang mới sẽ có đầy đủ content từ CMS

---

## 📝 Quy Trình Làm Việc Hàng Ngày

### Thêm Hoạ Sĩ Mới (Toàn Bộ)

1. **Chuẩn bị:**
   - Ảnh đại diện hoạ sĩ
   - Ảnh các tác phẩm (đặt tên nhất quán)
   - Thông tin tiểu sử
   - Thông tin CV/triển lãm

2. **Upload ảnh:**
   - Vào Media Library
   - Upload tất cả ảnh cùng lúc
   - Copy đường dẫn của ảnh đại diện

3. **Tạo profile hoạ sĩ:**
   - Vào Artists → Add New Artist
   - Điền đầy đủ thông tin
   - Paste đường dẫn ảnh
   - Save

4. **Thêm tác phẩm:**
   - Vào Artworks → Add New Artwork
   - Chọn hoạ sĩ vừa tạo
   - Thêm từng tác phẩm (có thể làm nhanh)
   - Save từng tác phẩm

5. **Kiểm tra:**
   - Nhấn View ở Artists page
   - Xem trang public
   - Kiểm tra responsive trên mobile

### Cập Nhật Thông Tin

- **Sửa bio:** Artists → Edit → Sửa biography → Save
- **Thêm ảnh:** Media Library → Upload → Copy path → Artworks → Add
- **Cập nhật triển lãm:** Exhibitions → Add Exhibition → Save

---

## 🎓 Tips & Tricks

### Làm Việc Nhanh Hơn

1. **Mở nhiều tab:** CMS hỗ trợ multi-tab
2. **Copy đường dẫn:** Paste lại cho nhiều artworks
3. **Đặt tên file:** Dùng format nhất quán để dễ quản lý
4. **Filter:** Dùng filter để tìm nhanh trong danh sách dài

### Tránh Lỗi

1. **Đường dẫn ảnh:** 
   - Từ CMS artists: `../images/artists/...`
   - Từ CMS artworks: `../../images/artists/...`
   - Kiểm tra relative path cho đúng

2. **Mã hoạ sĩ:** Không trùng nhau
3. **Mã tác phẩm:** Không trùng trong cùng hoạ sĩ
4. **Required fields:** Điền đầy đủ các trường bắt buộc (*)

### Optimize

1. **Ảnh:** Nén trước khi upload (< 500KB)
2. **Bio:** Giữ dưới 1000 từ cho loading nhanh
3. **Backup:** Export data mỗi tuần
4. **Clean:** Xoá ảnh không dùng khỏi Media Library

---

## 📞 Hỗ Trợ

- **Email:** nguyenthanhgallerie@gmail.com
- **Docs:** Đọc README.md và FEATURES.md
- **Examples:** Xem INTEGRATION-EXAMPLE.html

---

## ✅ Checklist Hoàn Thành

Sau khi setup CMS, check list này:

- [ ] Đã đăng nhập thành công
- [ ] Đã đổi mật khẩu mặc định
- [ ] Đã thêm ít nhất 1 hoạ sĩ
- [ ] Đã upload ít nhất 1 ảnh
- [ ] Đã thêm ít nhất 1 tác phẩm
- [ ] Đã test trên mobile
- [ ] Đã backup data lần đầu
- [ ] Đã xem trang public và kiểm tra
- [ ] Đã đọc DEPLOYMENT.md cho production
- [ ] Đã enable HTTPS (nếu production)

**Xong! CMS của bạn đã sẵn sàng sử dụng! 🎉**
