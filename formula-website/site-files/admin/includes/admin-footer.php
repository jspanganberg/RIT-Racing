
    </div><!-- /.content -->

<!-- ══════════ Photo Picker Modal ══════════ -->
<div class="picker-overlay" id="picker-overlay">
    <div class="picker-modal">
        <div class="picker-header">
            <h3><i class="fa fa-images"></i> Media Library</h3>
            <input type="text" class="picker-search" id="picker-search" placeholder="Search by filename...">
            <button class="picker-close" onclick="pickerClose()" title="Close">&times;</button>
        </div>
        <div class="picker-folders" id="picker-folders"></div>
        <div class="picker-grid" id="picker-grid">
            <div class="picker-empty"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
        </div>
        <div class="picker-footer">
            <div class="picker-selected-info" id="picker-info">No image selected</div>
            <div style="display:flex;gap:.5rem;">
                <button class="btn btn-secondary btn-sm" onclick="pickerClose()">Cancel</button>
                <button class="btn btn-primary btn-sm" id="picker-confirm" onclick="pickerConfirm()" disabled>Select Image</button>
            </div>
        </div>
    </div>
</div>

<script>
/* ── Photo Picker ── */
var _picker = {
    callback: null,
    selected: null,
    images: [],
    folder: 'all',
    folders: [],
};

/**
 * Open the picker modal.
 * @param {string}   defaultFolder  - Which folder to show initially (e.g. 'team', 'cars/electric', 'all')
 * @param {function} onSelect       - Callback(file, folder, path) when user confirms selection
 * @param {string}   currentValue   - Currently selected filename (to pre-highlight)
 */
function pickerOpen(defaultFolder, onSelect, currentValue) {
    _picker.callback = onSelect;
    _picker.selected = null;
    _picker.folder = defaultFolder || 'all';
    document.getElementById('picker-overlay').classList.add('open');
    document.getElementById('picker-search').value = '';
    document.getElementById('picker-confirm').disabled = true;
    document.getElementById('picker-info').textContent = 'No image selected';

    // Fetch all images
    fetch('/admin/api-media?folder=all')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            _picker.images = data.images || [];
            // Build folder list
            var fSet = {};
            _picker.images.forEach(function(img) { fSet[img.folder] = (fSet[img.folder]||0) + 1; });
            _picker.folders = Object.keys(fSet).sort();
            renderFolders(defaultFolder);
            renderGrid(currentValue);
        })
        .catch(function() {
            document.getElementById('picker-grid').innerHTML = '<div class="picker-empty">Error loading images</div>';
        });
}

function pickerClose() {
    document.getElementById('picker-overlay').classList.remove('open');
    _picker.callback = null;
}

function pickerConfirm() {
    if (_picker.selected && _picker.callback) {
        _picker.callback(_picker.selected.file, _picker.selected.folder, _picker.selected.path);
    }
    pickerClose();
}

function renderFolders(activeFolder) {
    var container = document.getElementById('picker-folders');
    var html = '<button class="picker-folder-btn ' + (activeFolder === 'all' ? 'active' : '') + '" onclick="filterFolder(\'all\')">All</button>';
    _picker.folders.forEach(function(f) {
        var label = f.replace('cars/','').replace('/',' / ');
        html += '<button class="picker-folder-btn ' + (f === activeFolder ? 'active' : '') + '" onclick="filterFolder(\'' + f + '\')">' + label + '</button>';
    });
    container.innerHTML = html;
}

function filterFolder(folder) {
    _picker.folder = folder;
    // Update active button
    document.querySelectorAll('.picker-folder-btn').forEach(function(b) {
        b.classList.toggle('active', b.textContent.trim().toLowerCase() === (folder === 'all' ? 'all' : folder.replace('cars/','').replace('/',' / ')));
    });
    // Re-render via renderGrid logic
    renderFolders(folder);
    renderGrid();
}

function renderGrid(preselect) {
    var grid = document.getElementById('picker-grid');
    var search = document.getElementById('picker-search').value.toLowerCase();
    var folder = _picker.folder;

    var filtered = _picker.images.filter(function(img) {
        if (folder !== 'all' && img.folder !== folder) return false;
        if (search && img.file.toLowerCase().indexOf(search) === -1) return false;
        return true;
    });

    if (filtered.length === 0) {
        grid.innerHTML = '<div class="picker-empty"><i class="fa fa-image" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>No images found</div>';
        return;
    }

    var html = '';
    filtered.forEach(function(img) {
        var isSelected = (preselect && img.file === preselect) || (_picker.selected && img.file === _picker.selected.file && img.folder === _picker.selected.folder);
        html += '<div class="picker-item' + (isSelected ? ' selected' : '') + '" onclick="pickerSelect(this, \'' + img.file.replace(/'/g,"\\'") + '\', \'' + img.folder + '\', \'' + img.path.replace(/'/g,"\\'") + '\')">';
        html += '<img src="' + img.path + '" alt="" loading="lazy">';
        html += '<div class="picker-item-name">' + img.file + '</div>';
        html += '</div>';

        if (isSelected) {
            _picker.selected = img;
            updatePickerInfo(img);
        }
    });
    grid.innerHTML = html;
}

function pickerSelect(el, file, folder, path) {
    // Deselect previous
    document.querySelectorAll('.picker-item.selected').forEach(function(e) { e.classList.remove('selected'); });
    el.classList.add('selected');
    _picker.selected = { file: file, folder: folder, path: path };
    document.getElementById('picker-confirm').disabled = false;
    updatePickerInfo(_picker.selected);
}

function updatePickerInfo(img) {
    document.getElementById('picker-info').innerHTML = 'Selected: <strong>' + img.file + '</strong> <span style="color:var(--gray);font-size:.7rem;">(' + img.folder + ')</span>';
}

// Live search filter
document.getElementById('picker-search').addEventListener('input', function() {
    renderGrid();
});

// Close on overlay click
document.getElementById('picker-overlay').addEventListener('click', function(e) {
    if (e.target === this) pickerClose();
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('picker-overlay').classList.contains('open')) {
        pickerClose();
    }
});

/**
 * Helper: Set up a "Browse Media" button on a form field.
 * @param {string} btnId       - ID of the browse button
 * @param {string} hiddenId    - ID of the hidden input to store the filename
 * @param {string} previewId   - ID of the preview container div
 * @param {string} folder      - Default folder to show in picker
 * @param {string} imgBasePath - Base path prefix for preview img src (e.g. '../assets/images/team/')
 */
function setupPicker(btnId, hiddenId, previewId, folder, imgBasePath) {
    var btn = document.getElementById(btnId);
    if (!btn) return;
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        var currentVal = document.getElementById(hiddenId).value;
        pickerOpen(folder, function(file, selFolder, path) {
            document.getElementById(hiddenId).value = file;
            var preview = document.getElementById(previewId);
            preview.innerHTML = '<div class="photo-preview-box">' +
                '<img src="' + path + '" alt="">' +
                '<div><div class="photo-name">' + file + '</div>' +
                '<button type="button" class="photo-clear" onclick="clearPicker(\'' + hiddenId + '\',\'' + previewId + '\')">Remove</button></div></div>';
        }, currentVal);
    });
}

function clearPicker(hiddenId, previewId) {
    document.getElementById(hiddenId).value = '';
    document.getElementById(previewId).innerHTML = '';
}
</script>

</main>
</body>
</html>
