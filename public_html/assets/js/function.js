function showLoading() {
    let loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        return;
    }
    let loadingHtml = `
        <div id="loadingOverlay" style="position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0, 0, 0, 0.25); display:flex; justify-content:center; align-items:center; z-index:9999;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loadingHtml);
}

function hideLoading() {
    
    let loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}