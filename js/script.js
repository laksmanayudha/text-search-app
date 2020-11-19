const inp = document.getElementById('files');
const max_file = 20;

// file melebihi batas
inp.addEventListener('change', (e) => {
    let fileLen =  inp.files.length;
    if (fileLen > max_file){
        alert('File melebihi batas maximum');
        inp.value = '';
    }
});
