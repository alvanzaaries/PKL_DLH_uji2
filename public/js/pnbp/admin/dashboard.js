// Menyinkronkan pilihan filter triwulan gabungan pada dashboard admin.
document.addEventListener('DOMContentLoaded', function () {
    const comboSelect = document.getElementById('combined_quarter_select');
    const inputQuarter = document.getElementById('input_quarter');
    const inputSampai = document.getElementById('input_sampai_quarter');

    if (comboSelect) {
        // Memecah nilai gabungan dan mengisi input triwulan yang sesuai.
        comboSelect.addEventListener('change', function () {
            const val = this.value; 
            inputQuarter.value = '';
            inputSampai.value = '';

            if (val) {
                const parts = val.split('-'); 
                const type = parts[0];
                const number = parts[1]; 

                if (type === 'q') {
                    inputQuarter.value = number;
                } else if (type === 's') {
                    inputSampai.value = number;
                }
            }
        });
    }
});