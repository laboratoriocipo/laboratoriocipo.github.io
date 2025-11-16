#!/bin/bash
# otimizar_agressivo.sh

PASTA_ORIGEM="./fotos"
PASTA_DESTINO="./fotos_otimizadas"
LARGURA_MAX="800"  # Máximo que um card precisaria
QUALIDADE="40"     # Mais agressivo

mkdir -p "$PASTA_DESTINO"

echo "Otimização agressiva para web (JPEG + WebP)"

for arquivo in "$PASTA_ORIGEM"/*.jpg "$PASTA_ORIGEM"/*.jpeg "$PASTA_ORIGEM"/*.JPG "$PASTA_ORIGEM"/*.JPEG "$PASTA_ORIGEM"/*.png "$PASTA_ORIGEM"/*.PNG; do
    if [ -f "$arquivo" ]; then
        nome_arquivo=$(basename "$arquivo")
        nome_sem_ext="${nome_arquivo%.*}"
        
        echo "Otimizando: $nome_arquivo"
        
        # JPEG otimizado
        convert "$arquivo" \
            -resize "${LARGURA_MAX}x${LARGURA_MAX}>" \
            -quality "$QUALIDADE" \
            -sampling-factor 4:2:0 \
            -strip \
            -interlace JPEG \
            -colorspace sRGB \
            "$PASTA_DESTINO/${nome_sem_ext}.jpg"
            
        # WebP (geralmente 30% menor que JPEG)
        convert "$arquivo" \
            -resize "${LARGURA_MAX}x${LARGURA_MAX}>" \
            -quality "$QUALIDADE" \
            -strip \
            "$PASTA_DESTINO/${nome_sem_ext}.webp"
            
        # Comparar tamanhos
        size_orig=$(stat -c%s "$arquivo")
        size_jpg=$(stat -c%s "$PASTA_DESTINO/${nome_sem_ext}.jpg")
        size_webp=$(stat -c%s "$PASTA_DESTINO/${nome_sem_ext}.webp")
        
        reducao_jpg=$((100 - (size_jpg * 100 / size_orig)))
        reducao_webp=$((100 - (size_webp * 100 / size_orig)))
        
        echo "  → JPEG: ${reducao_jpg}% menor | WebP: ${reducao_webp}% menor"
    fi
done

echo "Otimização concluída!"
