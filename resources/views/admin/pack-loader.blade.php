@extends('../layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<form id="packForm" enctype="multipart/form-data" style="max-width:700px; margin:auto; font-family:Arial,sans-serif;">
  <!-- Загрузка .zip -->
  <label for="file">Файл (.zip):</label>
  <div id="fileDrop" style="border:2px dashed #666; padding:20px; text-align:center; cursor:pointer; margin-bottom:10px;">
    Перетащите файл сюда или нажмите для выбора
    <input type="file" id="file" name="file" accept=".zip" style="display:none;">
  </div>
  <div id="fileInfo" style="margin:5px 0 15px 0; font-size:14px; color:#333;"></div>

  <!-- Остальные поля -->
  <label for="type">Тип пака:</label>
  <select id="type" name="type" style="width:100%; padding:6px; margin:6px 0 15px;">
    <option value="">-- Выберите тип --</option>
    <option value="resource">Ресурспак</option>
    <option value="mod">Мод</option>
    <option value="shader">Шейдер</option>
  </select>

  <label for="title">Заголовок:</label>
  <input id="title" name="title" maxlength="50" style="width:100%; padding:6px; margin:6px 0 15px;" type="text">

  <label>Предметы:</label>
  <table id="itemsTable" border="1" style="width:100%; border-collapse:collapse; margin-bottom:15px;">
    <thead>
      <tr><th style="padding:6px;">Название</th><th style="padding:6px;">Предмет</th></tr>
    </thead>
    <tbody>
      <tr>
        <td><input name="items[0][name]" style="width:100%; padding:6px;" type="text"></td>
        <td><input name="items[0][item]" style="width:100%; padding:6px;" type="text"></td>
      </tr>
    </tbody>
  </table>

  <label for="video">Ссылка на видео:</label>
  <input id="video" name="video" type="url" style="width:100%; padding:6px; margin:6px 0 15px;">

  <label>Изображения:</label>
  <div id="imageDrop" style="
    border:2px dashed #666;
    padding:20px;
    cursor:pointer;
    margin-bottom:10px;
    min-height:120px;
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    align-items:flex-start;
    position:relative;
  ">
    <input type="file" id="imageInput" accept="image/png, image/jpeg, image/webp" multiple style="display:none;">
  </div>

  <label for="franchise">Франшиза:</label>
  <input list="franchise-list" id="franchise" name="franchise" maxlength="50" style="width:100%; padding:6px; margin:6px 0 15px;" type="text">

  <datalist id="franchise-list">
    <option value="Naruto">
    <option value="One Piece">
    <option value="Attack on Titan">
    <option value="My Hero Academia">
    <option value="Demon Slayer">
  </datalist>

  <label for="categories">Категории (через запятую):</label>
  <input id="categories" name="categories" style="width:100%; padding:6px; margin:6px 0 15px;" type="text">

  <label for="description">Описание пака:</label>
  <textarea id="description" name="description" rows="4" style="width:100%; padding:6px; margin:6px 0 15px;"></textarea>

  <label for="meta_description">Мета-описание:</label>
  <textarea id="meta_description" name="meta_description" maxlength="160" rows="2" style="width:100%; padding:6px; margin:6px 0 15px;"></textarea>

  <button type="submit" style="padding:10px 20px; cursor:pointer;">Загрузить</button>
</form>

<script>
(() => {
  const form = document.getElementById('packForm');
  const fileInput = document.getElementById('file');
  const fileDrop = document.getElementById('fileDrop');
  const fileInfo = document.getElementById('fileInfo');
  const itemsTableBody = document.querySelector('#itemsTable tbody');
  const imageDrop = document.getElementById('imageDrop');
  const imageInput = document.getElementById('imageInput');

  let imageFiles = [];
  let dragStartIndex = null;

  // ZIP файл
  fileDrop.addEventListener('click', () => fileInput.click());
  fileDrop.addEventListener('dragover', e => { e.preventDefault(); fileDrop.style.background = '#eee'; });
  fileDrop.addEventListener('dragleave', () => { fileDrop.style.background = ''; });
  fileDrop.addEventListener('drop', e => {
    e.preventDefault();
    fileDrop.style.background = '';
    const file = e.dataTransfer.files[0];
    if (file && file.name.endsWith('.zip')) {
      const size = (file.size / 1024 / 1024).toFixed(2);
      fileInfo.innerHTML = `<strong>${file.name}</strong> (${size} MB)
        <button type="button" id="removeFileBtn" style="margin-left:10px; background:red; color:#fff; border:none; padding:4px 8px; cursor:pointer;">Удалить</button>`;
      fileDrop.style.display = 'none';
      fileInput.files = e.dataTransfer.files;
      document.getElementById('removeFileBtn').onclick = () => {
        fileInput.value = '';
        fileInfo.textContent = '';
        fileDrop.style.display = '';
      };
    } else {
      fileInfo.innerHTML = '<span style="color:red;">Неподдерживаемый файл</span>';
    }
  });
  fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (file) fileDrop.style.display = 'none';
    updateFileInfo(file);
  });
  const updateFileInfo = (file) => {
    if (!file) return;
    const size = (file.size / 1024 / 1024).toFixed(2);
    fileInfo.innerHTML = `<strong>${file.name}</strong> (${size} MB)`;
  };

  // Таблица предметов
  const onItemsInput = () => {
    const rows = [...itemsTableBody.querySelectorAll('tr')];
    let lastFilled = -1;
    rows.forEach((row, i) => {
      const [nameInput, itemInput] = row.querySelectorAll('input');
      const filled = nameInput.value.trim() || itemInput.value.trim();
      // removed required toggling
      if (filled) lastFilled = i;
    });
    if (lastFilled === rows.length - 1) addItemRow(rows.length);
    for (let i = rows.length - 2; i > lastFilled; i--) {
      itemsTableBody.removeChild(rows[i]);
    }
  };
  const addItemRow = (index) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><input name="items[${index}][name]" style="width:100%; padding:6px;" type="text"></td>
      <td><input name="items[${index}][item]" style="width:100%; padding:6px;" type="text"></td>`;
    itemsTableBody.appendChild(row);
    row.querySelectorAll('input').forEach(i => i.addEventListener('input', onItemsInput));
  };
  itemsTableBody.querySelectorAll('input').forEach(i => i.addEventListener('input', onItemsInput));

  // Изображения
  const createUploadPlaceholder = () => {
    const el = document.createElement('div');
    el.id = 'uploadPlaceholder';
    el.textContent = '+';
    Object.assign(el.style, {
      width: '200px', height: '200px', border: '2px dashed #666', display: 'flex',
      justifyContent: 'center', alignItems: 'center', fontSize: '48px', fontWeight: 'bold',
      cursor: 'pointer', userSelect: 'none', backgroundColor: '#fafafa', flexShrink: 0
    });
    return el;
  };
  const renderImageList = () => {
    imageDrop.querySelectorAll('.image-item, #uploadPlaceholder').forEach(el => el.remove());
    imageFiles.forEach((file, i) => {
      const wrapper = document.createElement('div');
      wrapper.className = 'image-item';
      wrapper.draggable = true;
      wrapper.dataset.index = i;
      Object.assign(wrapper.style, {
        border: '1px solid #ccc', padding: '5px', background: '#f8f8f8', width: '200px',
        margin: '10px', position: 'relative', flexShrink: 0, userSelect: 'none'
      });

      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      img.draggable = false;
      img.style.maxWidth = '100%';
      img.onload = () => URL.revokeObjectURL(img.src);

      const input = document.createElement('input');
      input.type = 'text';
      input.placeholder = 'Описание изображения';
      input.name = `images[${i}][desc]`;
      input.style.width = '100%';
      input.style.marginTop = '5px';

      const removeBtn = document.createElement('div');
      removeBtn.textContent = '×';
      Object.assign(removeBtn.style, {
        position: 'absolute', top: '2px', right: '6px', fontSize: '18px',
        color: 'red', cursor: 'pointer', fontWeight: 'bold'
      });
      removeBtn.onclick = () => {
        imageFiles.splice(i, 1);
        renderImageList();
      };

      wrapper.append(removeBtn, img, input);
      wrapper.addEventListener('dragstart', e => { dragStartIndex = +e.currentTarget.dataset.index; e.currentTarget.style.opacity = '0.5'; });
      wrapper.addEventListener('dragend', e => { e.currentTarget.style.opacity = '1'; });
      wrapper.addEventListener('dragover', e => { e.preventDefault(); wrapper.style.border = '2px dashed #333'; });
      wrapper.addEventListener('dragleave', () => { wrapper.style.border = '1px solid #ccc'; });
      wrapper.addEventListener('drop', e => {
        e.preventDefault();
        const to = +e.currentTarget.dataset.index;
        const [moved] = imageFiles.splice(dragStartIndex, 1);
        imageFiles.splice(to, 0, moved);
        renderImageList();
      });

      imageDrop.appendChild(wrapper);
    });
    imageDrop.appendChild(createUploadPlaceholder());
  };
  const handleNewImages = (files) => {
    for (const file of files) {
      if (['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
        imageFiles.push(file);
      }
    }
    renderImageList();
  };
  imageDrop.addEventListener('click', e => {
    if (e.target.id === 'uploadPlaceholder') imageInput.click();
  });
  imageDrop.addEventListener('dragover', e => { e.preventDefault(); imageDrop.style.background = '#eee'; });
  imageDrop.addEventListener('dragleave', () => { imageDrop.style.background = ''; });
  imageDrop.addEventListener('drop', e => {
    e.preventDefault();
    imageDrop.style.background = '';
    handleNewImages(e.dataTransfer.files);
  });
  imageInput.addEventListener('change', () => {
    handleNewImages(imageInput.files);
    imageInput.value = '';
  });
  renderImageList();

  // Отправка формы
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Проверка на наличие обязательных данных — добавь, если надо
    if (!fileInput.files[0]) {
      alert('Пожалуйста, загрузите ZIP-файл');
      return;
    }
    if (imageFiles.length === 0) {
      alert('Пожалуйста, загрузите хотя бы одно изображение');
      return;
    }

    const formData = new FormData();

    // Добавляем ZIP файл
    formData.append('file', fileInput.files[0]);

    // Добавляем остальные простые поля
    formData.append('type', form.type.value);
    formData.append('title', form.title.value);

    // Добавляем предметы из таблицы
    const itemRows = [...itemsTableBody.querySelectorAll('tr')];
    itemRows.forEach((row, i) => {
      const inputs = row.querySelectorAll('input');
      if (inputs.length === 2) {
        const name = inputs[0].value.trim();
        const item = inputs[1].value.trim();
        if (name || item) {
          formData.append(`items[${i}][name]`, name);
          formData.append(`items[${i}][item]`, item);
        }
      }
    });

    formData.append('video', form.video.value);
    formData.append('franchise', form.franchise.value);
    formData.append('categories', form.categories.value);
    formData.append('description', form.description.value);
    formData.append('meta_description', form.meta_description.value);

    // Добавляем изображения и описания
    imageFiles.forEach((file, i) => {
      formData.append(`images[${i}][file]`, file);
      const descInput = document.querySelector(`input[name="images[${i}][desc]"]`);
      const desc = descInput ? descInput.value : '';
      formData.append(`images[${i}][desc]`, desc);
    });

    // Отправляем на сервер (пример POST-запроса)
    try {
      const response = await fetch('/admin/load/pack', { // Заменить URL на твой роут
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
      });

      if (!response.ok) {
        const errorText = await response.text();
        alert('Ошибка при загрузке: ' + errorText);
        return;
      }

      const result = await response.json();
      alert('Пак успешно загружен!');
      form.reset();
      imageFiles = [];
      renderImageList();
      fileDrop.style.display = '';
      fileInfo.textContent = '';
    } catch (error) {
      alert('Ошибка сети: ' + error.message);
    }
  });
})();
</script>
@endsection
