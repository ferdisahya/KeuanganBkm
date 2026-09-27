import json
import re

with open('extracted_bkm_rows.json', 'r', encoding='utf-8') as f:
    rows = json.load(f)

print(f"Loaded {len(rows)} rows from extracted_bkm_rows.json")

# Master data unique collection
rekenings = {} # no_rek -> nama_rek
subrekenings = {} # no_sub -> nama_sub
items = {} # no_item -> nama_item

def escape_sql(s):
    if s is None:
        return ''
    return s.replace('\\', '\\\\').replace("'", "''")

def parse_number(num_str):
    if not num_str:
        return '0.00'
    cleaned = num_str.strip().replace(' ', '').replace('.', '').replace(',', '.')
    return f"{float(cleaned):.2f}"

def format_date(d_str):
    # DD/MM/YYYY -> YYYY-MM-DD
    parts = d_str.strip().split('/')
    if len(parts) == 3:
        return f"{parts[2]}-{parts[1].zfill(2)}-{parts[0].zfill(2)}"
    return d_str

for r in rows:
    if r['no_rek']:
        rekenings[r['no_rek']] = r['nama_rek']
    if r['no_sub_rek']:
        subrekenings[r['no_sub_rek']] = r['nama_sub_rek']
    if r['no_item']:
        items[r['no_item']] = r['nama_item']

sql_lines = []
sql_lines.append("-- ============================================================================")
sql_lines.append("-- SQL SCRIPT IMPORT KAS BKM PERIODE 13 AGUSTUS 2026 S/D 12 SEPTEMBER 2026")
sql_lines.append("-- Sumber File: Laporan_Kas_BKM_13Agu_12Sep2026.pdf")
sql_lines.append(f"-- Total Transaksi: {len(rows)} baris")
sql_lines.append("-- Terdiri dari: 4 Transaksi Debet & 245 Transaksi Kredit")
sql_lines.append("-- Database Target: u957149181_keuangan / keuangan_bkm")
sql_lines.append("-- ============================================================================\n")
sql_lines.append("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';")
sql_lines.append("SET time_zone = '+00:00';")
sql_lines.append("START TRANSACTION;\n")

# 1. Master Rekening BKM
sql_lines.append("-- ----------------------------------------------------------------------------")
sql_lines.append(f"-- 1. MASTER REKENING BKM ({len(rekenings)} Rekening)")
sql_lines.append("-- ----------------------------------------------------------------------------")
for code in sorted(rekenings.keys()):
    name = escape_sql(rekenings[code])
    sql_lines.append(
        f"INSERT INTO `rekening_bkm` (`nomer_rekening`, `nama_rekening`, `created_at`, `updated_at`) "
        f"SELECT '{code}', '{name}', NOW(), NOW() "
        f"FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `rekening_bkm` WHERE `nomer_rekening` = '{code}');"
    )
sql_lines.append("")

# 2. Master Sub Rekening BKM
sql_lines.append("-- ----------------------------------------------------------------------------")
sql_lines.append(f"-- 2. MASTER SUB REKENING BKM ({len(subrekenings)} Sub Rekening)")
sql_lines.append("-- ----------------------------------------------------------------------------")
for code in sorted(subrekenings.keys()):
    name = escape_sql(subrekenings[code])
    sql_lines.append(
        f"INSERT INTO `subrekening_bkm` (`nomer_subrekening`, `nama_subrekening`, `created_at`, `updated_at`) "
        f"SELECT '{code}', '{name}', NOW(), NOW() "
        f"FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `subrekening_bkm` WHERE `nomer_subrekening` = '{code}');"
    )
sql_lines.append("")

# 3. Master Item BKM
sql_lines.append("-- ----------------------------------------------------------------------------")
sql_lines.append(f"-- 3. MASTER ITEM BKM ({len(items)} Item)")
sql_lines.append("-- ----------------------------------------------------------------------------")
for code in sorted(items.keys()):
    name = escape_sql(items[code])
    sql_lines.append(
        f"INSERT INTO `item_bkm` (`nomer_item`, `nama_item`, `created_at`, `updated_at`) "
        f"SELECT '{code}', '{name}', NOW(), NOW() "
        f"FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `item_bkm` WHERE `nomer_item` = '{code}');"
    )
sql_lines.append("")

# 4. Transaksi Kas BKM
sql_lines.append("-- ----------------------------------------------------------------------------")
sql_lines.append(f"-- 4. TRANSAKSI KAS BKM ({len(rows)} Transaksi)")
sql_lines.append("-- ----------------------------------------------------------------------------")

total_debet = 0.0
total_kredit = 0.0

for idx, r in enumerate(rows, 1):
    tgl = format_date(r['tanggal'])
    uraian = escape_sql(r['uraian'])
    debet_val = parse_number(r['debet'])
    kredit_val = parse_number(r['kredit'])
    saldo_val = parse_number(r['saldo_kas'])
    
    total_debet += float(debet_val)
    total_kredit += float(kredit_val)
    
    no_rek = r['no_rek']
    no_sub = r['no_sub_rek']
    no_item = r['no_item']
    
    rek_subq = f"(SELECT `id` FROM `rekening_bkm` WHERE `nomer_rekening` = '{no_rek}' LIMIT 1)" if no_rek else "NULL"
    sub_subq = f"(SELECT `id` FROM `subrekening_bkm` WHERE `nomer_subrekening` = '{no_sub}' LIMIT 1)" if no_sub else "NULL"
    item_subq = f"(SELECT `id` FROM `item_bkm` WHERE `nomer_item` = '{no_item}' LIMIT 1)" if no_item else "NULL"
    
    sql_lines.append(
        f"-- #{idx} | Tgl: {tgl} | {r['uraian'][:50]}"
    )
    sql_lines.append(
        f"INSERT INTO `kas_bkm` "
        f"(`tanggal`, `uraian`, `debet`, `kredit`, `saldo_kas`, `keterangan`, `rekening_bkm_id`, `subrekening_bkm_id`, `item_bkm_id`, `created_at`, `updated_at`) "
        f"VALUES ('{tgl}', '{uraian}', {debet_val}, {kredit_val}, {saldo_val}, NULL, {rek_subq}, {sub_subq}, {item_subq}, NOW(), NOW());"
    )

sql_lines.append("\nCOMMIT;\n")

output_file = 'import_kas_bkm_13agu_12sep2026.sql'
with open(output_file, 'w', encoding='utf-8') as f:
    f.write('\n'.join(sql_lines))

print(f"Generated {output_file} successfully!")
print(f"Total SQL Lines: {len(sql_lines)}")
print(f"Total Debet: Rp {total_debet:,.2f}")
print(f"Total Kredit: Rp {total_kredit:,.2f}")
print(f"Final Saldo in row 249: Rp {float(parse_number(rows[-1]['saldo_kas'])):,.2f}")
