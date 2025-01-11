from PIL import Image
import sys

def process_image(input_path, output_path, thumbnail_path, target_size):
    try:
        # Ouvrir l'image en mode paresseux pour minimiser la mémoire utilisée
        with Image.open(input_path) as img:
            # Déterminer si l'image est en mode paysage ou portrait
            if img.width >= img.height:  # Mode paysage
                aspect_ratio = img.height / img.width
                new_width = target_size
                new_height = int(target_size * aspect_ratio)
            else:  # Mode portrait
                aspect_ratio = img.width / img.height
                new_height = target_size
                new_width = int(target_size * aspect_ratio)

            # Redimensionner l'image principale
            resized_img = img.resize((new_width, new_height), Image.LANCZOS)
            resized_img.save(output_path, "WEBP", quality=70, optimize=True)

            # Créer une miniature (hauteur fixe à 300px)
            thumbnail_height = 300
            thumbnail = resized_img.copy()
            thumbnail.thumbnail((new_width, thumbnail_height), Image.LANCZOS)
            thumbnail.save(thumbnail_path, "WEBP", quality=80, optimize=True)

            print("Image traitée avec succès")
    except Exception as e:
        print(f"Erreur lors du traitement de l'image : {e}")
        sys.exit(1)

if __name__ == "__main__":
    input_path = sys.argv[1]
    output_path = sys.argv[2]
    thumbnail_path = sys.argv[3]
    target_size = int(sys.argv[4])  # Taille cible (largeur pour paysage ou hauteur pour portrait)
    process_image(input_path, output_path, thumbnail_path, target_size)
