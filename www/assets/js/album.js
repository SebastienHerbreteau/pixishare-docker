import 'justifiedGallery/dist/css/justifiedGallery.css';
import 'justifiedGallery/dist/js/jquery.justifiedGallery.js';

let photos = document.querySelectorAll('.photo')
let modal = document.querySelector('.modal')

$('.content').justifiedGallery({
    rowHeight : 200,
    lastRow : 'left',
    margins : 5,
    border: 150
});

photos.forEach(photo => {
    photo.addEventListener('click', function () {
        let id = photo.dataset.id
        fetch('/gallery/album/photo/' + id)
            .then(response => response.text().then(function(content) {
                modal.innerHTML = content
                modal.style.display = 'flex'
                modal.addEventListener('click', () => modal.style.display = 'none')
            }))
    })
})

