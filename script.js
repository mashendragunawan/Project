function tampilkanPopup() {
    // Ambil nilai dari input
    const nama = document.getElementById("nama").value;
    const tahunLahir = document.getElementById("tahunLahir").value;
  
    // Hitung umur
    const tahunSekarang = new Date().getFullYear();
    const umur = tahunSekarang - tahunLahir;
  
    // Tampilkan hasil di popup
    const hasilElement = document.getElementById("hasil");
    hasilElement.textContent = `Nama: ${nama}, Umur: ${umur} tahun`;
  
    // Tampilkan popup
    const popup = document.getElementById("popup");
    popup.classList.remove("popup-hidden");
    popup.classList.add("popup-tampil");
  }