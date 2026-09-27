import pypdf
import re
import json

reader = pypdf.PdfReader('Laporan_Kas_BKM_13Agu_12Sep2026.pdf')

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

pattern = re.compile(r'1\s+0\s+0\s+1\s+([\d\.\-]+)\s+([\d\.\-]+)\s+cm\s*(?:q\s*)?(?:[^\n]*\s*)?BT(.*?)ET', re.DOTALL)

all_rows = []

for page_idx, page in enumerate(reader.pages):
    contents = page.get_contents()
    raw_data = b''.join(c.get_data() for c in contents) if isinstance(contents, list) else contents.get_data()
    decoded = raw_data.decode('latin1', errors='ignore')
    matches = pattern.findall(decoded)
    
    items = []
    for x_str, y_str, bt in matches:
        x = float(x_str)
        y = float(y_str)
        tjs = re.findall(r'\((.*?)\)\s*Tj', bt)
        text = ' '.join(tjs).replace('\\(', '(').replace('\\)', ')').replace('\\\\', '\\').strip()
        if not text or 'LAPORAN KAS BKM' in text or 'Periode' in text:
            continue
        if text in ['TANGGAL', 'NO REK', 'NAMA REK', 'NO. SUB REK', 'NAMA SUB REK', 'NO ITEM', 'NAMA SUB ITEM', 'URAIAN', 'DEBET (Rp)', 'KREDIT (Rp)', 'SALDO KAS']:
            continue
        items.append({'x': x, 'y': y, 'col': get_column(x), 'text': text})
    
    date_items = [it for it in items if it['col'] == 'tanggal' and re.match(r'^\d{2}/\d{2}/\d{4}$', it['text'])]
    date_items.sort(key=lambda it: -it['y'])
    
    for d_idx, d_item in enumerate(date_items):
        cur_y = d_item['y']
        next_y = date_items[d_idx+1]['y'] if d_idx+1 < len(date_items) else 0
        
        row_elements = [it for it in items if next_y < it['y'] <= cur_y + 3]
        row_elements.sort(key=lambda it: it['x'])
        
        row_dict = {
            'tanggal': '',
            'no_rek': '',
            'nama_rek': '',
            'no_sub_rek': '',
            'nama_sub_rek': '',
            'no_item': '',
            'nama_item': '',
            'uraian': '',
            'debet': '',
            'kredit': '',
            'saldo_kas': ''
        }
        for it in row_elements:
            col = it['col']
            if row_dict[col]:
                row_dict[col] += ' ' + it['text']
            else:
                row_dict[col] = it['text']
        
        row_dict['page'] = page_idx + 1
        all_rows.append(row_dict)

print(f"Total extracted rows: {len(all_rows)}")
print(f"First row: {all_rows[0]}")
print(f"Last row: {all_rows[-1]}")

debet_count = sum(1 for r in all_rows if r['debet'])
kredit_count = sum(1 for r in all_rows if r['kredit'])
print(f"Rows with debet: {debet_count}, rows with kredit: {kredit_count}")

# Check any debet rows
if debet_count > 0:
    print("Debet rows samples:")
    for r in all_rows:
        if r['debet']:
            print(" ", r)

# Save to json for inspection
with open('extracted_bkm_rows.json', 'w', encoding='utf-8') as f:
    json.dump(all_rows, f, indent=2, ensure_ascii=False)
print("Saved to extracted_bkm_rows.json")
