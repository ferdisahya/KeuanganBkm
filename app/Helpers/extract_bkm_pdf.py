import sys
import os
import json
import re
import pypdf

def extract_bkm_pdf(pdf_path, output_json_path):
    if not os.path.exists(pdf_path):
        print(f"Error: File '{pdf_path}' not found.", file=sys.stderr)
        sys.exit(1)

    reader = pypdf.PdfReader(pdf_path)
    total_pages = len(reader.pages)

    date_pattern = re.compile(r'^\d{1,2}/\d{1,2}/\d{4}$')
    rows = []

    for page_idx in range(total_pages):
        try:
            text = reader.pages[page_idx].extract_text()
        except Exception as e:
            continue

        if not text:
            continue

        lines = [l.strip() for l in text.split('\n') if l.strip()]

        i = 0
        while i < len(lines):
            line = lines[i]

            # Skip header labels
            if line.upper() in ['LAPORAN KAS', 'TANGGAL', 'URAIAN', 'SALDO KAS (RP)', 'SALDO KAS']:
                i += 1
                continue

            # Check if line is a date (DD/MM/YYYY)
            if date_pattern.match(line):
                tanggal = line
                i += 1
                uraian_parts = []
                saldo_str = None

                while i < len(lines):
                    next_line = lines[i]
                    cleaned_val = next_line.replace(' ', '')
                    
                    # Saldo amount pattern: e.g. -12.000.000 or 75.210.276
                    if re.match(r'^-?\d{1,3}(?:\.\d{3})*(?:,\d+)?$', cleaned_val) or re.match(r'^-?\d+(?:,\d+)?$', cleaned_val):
                        saldo_str = cleaned_val
                        i += 1
                        break
                    elif date_pattern.match(next_line):
                        break
                    else:
                        uraian_parts.append(next_line)
                        i += 1

                uraian = ' '.join(uraian_parts)
                rows.append({
                    'tanggal': tanggal,
                    'uraian': uraian,
                    'saldo': saldo_str
                })
            else:
                i += 1

    with open(output_json_path, 'w', encoding='utf-8') as f:
        json.dump(rows, f, ensure_ascii=False)

    print(f"SUCCESS: Extracted {len(rows)} rows across {total_pages} pages to {output_json_path}")

if __name__ == '__main__':
    if len(sys.argv) < 3:
        print("Usage: python extract_bkm_pdf.py <input_pdf_path> <output_json_path>")
        sys.exit(1)

    pdf_in = sys.argv[1]
    json_out = sys.argv[2]
    extract_bkm_pdf(pdf_in, json_out)
