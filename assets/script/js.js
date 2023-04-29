window.onload = () => {
    let list = document.getElementById("files");

    let uploader = new plupload.Uploader({
        browse_button: "setFile",
        url: "upload.php",
        chunk_size: "10mb",
        init: {
            PostInit: () => list.innerHTML = "<div>UPLOAD FILE</div>",
            FilesAdded: (up, files) => {
                plupload.each(files, file => {
                    let row = document.createElement("div");
                    row.id = file.id;
                    row.innerHTML = `${file.name} (${plupload.formatSize(file.size)}) <strong></strong>`;
                    list.appendChild(row);
                });
                uploader.start();
            },
            UploadProgress: (up, file) => {
                document.querySelector(`#${file.id} strong`).innerHTML = `${file.percent}%`;
            },
            Error: (up, err) => console.error(err)
        }
    });
    uploader.init();
};
