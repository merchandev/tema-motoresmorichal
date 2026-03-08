import os

file_path = r"c:\Users\merch\OneDrive\Documentos\TOYOTA\CONTROL-DE-VERSION-VERSION-ESTABLE-0511202 (1)\style.css"

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add Global Variables
target_1 = """  --header-h: var(--header-h-desktop);
}"""
replacement_1 = """  --header-h: var(--header-h-desktop);
  
  /* Nuevas variables para unificar espaciado */
  --section-spacing-y: clamp(60px, 8vh, 100px);
  --gap-default: clamp(20px, 3vw, 32px);
}"""

# 2. Fix Image Sizing (Used Car)
target_2 = """  .vehiculo-single .vs-image img {
    height: clamp(220px, 46vw, 420px);
    width: 100%;
    object-fit: cover;
  }"""
replacement_2 = """  .vehiculo-single .vs-image img {
    height: auto;
    min-height: 220px;
    width: 100%;
    object-fit: contain;
    margin: 0 auto;
  }"""

# 3. Fix Image Sizing (New Car)
target_3 = """  .vp-car-stage {
    min-height: 240px;
    padding: 14px;
  }"""
replacement_3 = """  .vp-car-stage {
    min-height: 200px;
    padding: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .vp-car-img {
    max-height: 280px;
    width: 100%;
    height: auto;
    object-fit: contain;
  }"""

# Apply replacements
new_content = content.replace(target_1, replacement_1)
new_content = new_content.replace(target_2, replacement_2)
new_content = new_content.replace(target_3, replacement_3)

if content == new_content:
    print("No changes made. Targets not found.")
    # Debugging: print part of the content to check
    print("Snippet around line 30:")
    print(content[800:1000]) # Approximate location
else:
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Successfully patched style.css")
