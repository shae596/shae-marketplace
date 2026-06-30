#!/usr/bin/env python3
"""Copy SHAE application files into professor Laravel base."""
import shutil
from pathlib import Path

SHAE = Path(r"C:\Users\HP\Projects\shae")
TARGET = Path(r"C:\Users\HP\Projects\examen-php-laravel-l3")

def copy_tree(src: Path, dst: Path):
    if not src.exists():
        print(f"SKIP missing: {src}")
        return 0
    count = 0
    for item in src.rglob("*"):
        if item.is_file():
            rel = item.relative_to(src)
            dest = dst / rel
            dest.parent.mkdir(parents=True, exist_ok=True)
            shutil.copy2(item, dest)
            count += 1
    return count

def main():
    total = 0
    total += copy_tree(SHAE / "app", TARGET / "app")
    total += copy_tree(SHAE / "resources" / "views", TARGET / "resources" / "views")
    total += copy_tree(SHAE / "resources" / "lang", TARGET / "resources" / "lang")
    total += copy_tree(SHAE / "docs", TARGET / "docs")

    mig_dst = TARGET / "database" / "migrations"
    mig_dst.mkdir(parents=True, exist_ok=True)
    for f in (SHAE / "database" / "migrations").glob("2024*.php"):
        shutil.copy2(f, mig_dst / f.name)
        total += 1
    for f in (SHAE / "database" / "migrations").glob("*personal_access*"):
        shutil.copy2(f, mig_dst / f.name)
        total += 1

    shutil.copy2(SHAE / "database" / "seeders" / "DatabaseSeeder.php", TARGET / "database" / "seeders" / "DatabaseSeeder.php")
    total += 1

    for name in ("web.php", "api.php"):
        shutil.copy2(SHAE / "routes" / name, TARGET / "routes" / name)
        total += 1

    shutil.copy2(SHAE / "config" / "shae.php", TARGET / "config" / "shae.php")
    total += 1

    if not (TARGET / "README-EXAMEN.md").exists():
        shutil.copy2(TARGET / "README.md", TARGET / "README-EXAMEN.md")
    shutil.copy2(SHAE / "README.md", TARGET / "README-SHAE.md")

    controllers = list((TARGET / "app" / "Http" / "Controllers").rglob("*.php"))
    print(f"OK copied {total} files")
    print(f"Controllers in target: {len(controllers)}")
    print(f"api.php exists: {(TARGET / 'routes' / 'api.php').exists()}")

if __name__ == "__main__":
    main()
