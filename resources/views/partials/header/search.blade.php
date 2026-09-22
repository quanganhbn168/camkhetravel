<div class="modal fade" id="header-search-modal" tabindex="-1" aria-labelledby="header-search-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-4" id="header-search-title">Tìm dịch vụ & bài viết</h2>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Đóng tìm kiếm"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('search') }}" method="GET" role="search">
                    <label class="visually-hidden" for="header-search-query">Từ khóa tìm kiếm</label>
                    <div class="input-group">
                        <input class="form-control" id="header-search-query" name="q" type="search" value="{{ request('q') }}" placeholder="Ví dụ: thi công PCCC, bảo trì báo cháy…" maxlength="100">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </form>
                <p class="small text-body mt-3 mb-0">Tìm trong dịch vụ và bài viết đã xuất bản.</p>
            </div>
        </div>
    </div>
</div>
