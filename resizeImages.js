const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const inputDir = path.join(__dirname, 'img');
const outputDir = path.join(__dirname, 'img_min');

// Verificar si la carpeta de salida existe, si no, crearla
if (!fs.existsSync(outputDir)){
    fs.mkdirSync(outputDir);
}

// Obtener todos los archivos de la carpeta de entrada
fs.readdir(inputDir, (err, files) => {
    if (err) {
        console.error('Error leyendo la carpeta:', err);
        return;
    }

    // Filtrar solo archivos de imagen
    const images = files.filter(file => /\.(jpg|jpeg|png|gif|bmp)$/i.test(file));

    // Redimensionar cada imagen
    images.forEach(file => {
        const inputPath = path.join(inputDir, file);
        const outputPath = path.join(outputDir, file); // Mismo nombre en la nueva carpeta

        sharp(inputPath)
            .resize({
                width: 600, // Ajusta el ancho según tus necesidades
                fit: sharp.fit.cover // Método de ajuste
            })
            .toFile(outputPath, (err, info) => {
                if (err) {
                    console.error('Error procesando la imagen:', err);
                } else {
                    console.log(`Se ha redimensionado ${file} y guardado como ${outputPath}`);
                }
            });
    });
});