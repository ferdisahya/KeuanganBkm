import pypdf
import re
import json

reader = pypdf.PdfReader('Laporan_Kas_BKM_13Agu_12Sep2026.pdf')
print(f"Total pages: {len(reader.pages)}")

# Column mapping based on X ranges
def get_column(x):
    if x < 50:
        return 'tanggal'
    elif x < 100:
        return 'no_rek'
    elif x < 200:
        return 'nama_rek'
    elif x < 290:
        return 'no_sub_rek'
    elif x < 400:
        return 'nama_sub_rek'
    elif x < 490:
        return 'no_item'
    elif x < 600:
        return 'nama_item'
    elif x < 820:
        return 'uraian'
    elif x < 920:
        return 'debet'
    elif x < 1010:
        return 'kredit'
    else:
        return 'saldo_kas'

all_pages_data = []

pattern = re.compile(r'1\s+0\s+0\s+1\s+([\d\.\-]+)\s+([\d\.\-]+)\s+cm\s*(?:q\s*)?(?:[^\n]*\s*)?BT(.*?)ET', re.DOTALL)

for page_idx, page in enumerate(reader.pages):
    contents = page.get_contents()
    if isinstance(contents, list):
        raw_data = b''.join(c.get_data() for c in contents)
    else:
        raw_data = contents.get_data()
    decoded = raw_data.decode('latin1', errors='ignore')
    matches = pattern.findall(decoded)
    
    # Collect items with x, y, text
    items = []
    for x_str, y_str, bt in matches:
        x = float(x_str)
        y = float(y_str)
        
        # Extract text strings inside Tj
        tjs = re.findall(r'\((.*?)\)\s*Tj', bt)
        text = ' '.join(tjs).replace('\\(', '(').replace('\\)', ')').replace('\\\\', '\\').strip()
        
        # Skip title headers
        if 'LAPORAN KAS BKM' in text or 'Periode' in text:
            continue
        if text in ['TANGGAL', 'NO REK', 'NAMA REK', 'NO. SUB REK', 'NAMA SUB REK', 'NO ITEM', 'NAMA SUB ITEM', 'URAIAN', 'DEBET (Rp)', 'KREDIT (Rp)', 'SALDO KAS']:
            continue
        
        if not text:
            continue
            
        items.append({
            'x': x,
            'y': y,
            'col': get_column(x),
            'text': text
        })
        
    print(f"Page {page_idx + 1}: {len(items)} text elements found.")
    all_pages_data.append(items)
