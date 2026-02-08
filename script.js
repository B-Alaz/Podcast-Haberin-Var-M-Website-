console.log('=== PODCAST PLAYER IS RUNNING ===');

let currentlyPlaying = null; // Şu an çalan buton
let audioPlayer = null;      // Ses nesnesi

document.addEventListener('DOMContentLoaded', () => {
    const API_ENDPOINT = './api.php'; 

    const loadEpisodes = async () => {
        const sagBlok = document.querySelector('.sag-blok');
        if (!sagBlok) return;
        
        try {
            sagBlok.innerHTML = '<p style="text-align:center; color:#ccc; padding:40px;">Bölümler yükleniyor...</p>';
            
            const response = await fetch(API_ENDPOINT);
            const result = await response.json();
            const bolumData = result.data || [];

            if (bolumData.length > 0) {
                const bolumListesiHTML = bolumData.map((bolum, index) => {
                    const isActive = bolum.playable == 1; 
                    const buttonClass = isActive ? 'oynat-butonu' : 'oynat-butonu pasif';
                    const buttonText = isActive ? '▶️ OYNAT' : 'BEKLEMEDE';
                    const disabledAttr = isActive ? '' : 'disabled';
                    
                    return `
                        <article class="bolum" data-episode-id="${index}">
                            <div class="bolum-detay">
                                <h4>${bolum.name}</h4>
                                <span class="sure">Süre: ${bolum.duration}</span>
                            </div>
                            <p class="bolum-aciklama">${bolum.description}</p>
                            <button class="${buttonClass}" ${disabledAttr} 
                                    data-episode="${bolum.name}" 
                                    data-file="${bolum.file_path}">
                                ${buttonText}
                            </button>
                        </article>
                    `;
                }).join('');

                sagBlok.innerHTML = `<h3>📚 Tüm Bölümler</h3>${bolumListesiHTML}`;
                setupPlayButtons();
                
            } else {
                sagBlok.innerHTML = '<h3>📚 Tüm Bölümler</h3><p>Henüz bölüm yok.</p>';
            }
        } catch (error) {
            console.error("Hata:", error);
        }
    };

    function setupPlayButtons() {
        const playButtons = document.querySelectorAll('.oynat-butonu:not(.pasif)');
        
        playButtons.forEach((button) => {
            button.addEventListener('click', function() {
                const filePath = this.getAttribute('data-file'); // Butondan dosya yolunu al
                
                // 1. Eğer zaten bu çalıyor ise: DURDUR
                if (currentlyPlaying === this) {
                    stopCurrentlyPlaying();
                    return;
                }

                // 2. Başka bir şey çalıyorsa onu önce durdur
                if (currentlyPlaying) {
                    stopCurrentlyPlaying();
                }

                // 3. Yeni sesi yükle ve oynat
                audioPlayer = new Audio(filePath);
                audioPlayer.play().catch(e => console.error("Ses dosyası bulunamadı:", e));

                // 4. Görseli güncelle
                this.textContent = '⏹️ DURDUR';
                this.classList.add('playing');
                this.closest('.bolum').classList.add('playing');
                currentlyPlaying = this;

                // Ses bittiğinde otomatik durdurma görseli
                audioPlayer.onended = () => stopCurrentlyPlaying();
            });
        });
    }
    
    function stopCurrentlyPlaying() {
        if (currentlyPlaying) {
            // Sesi durdur ve nesneyi sıfırla
            if (audioPlayer) {
                audioPlayer.pause();
                audioPlayer = null;
            }

            currentlyPlaying.textContent = '▶️ OYNAT';
            currentlyPlaying.classList.remove('playing');
            currentlyPlaying.closest('.bolum').classList.remove('playing');
            currentlyPlaying = null;
        }
    }

    loadEpisodes();
});