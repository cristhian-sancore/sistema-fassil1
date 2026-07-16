with open(r'D:\BKP OUTROS ANTIGOS\sistema\init-db\01-grupofas_sistema.sql', 'r', encoding='utf-8', errors='ignore') as f:
    lines = f.readlines()
    print(lines[1192][:4000])
