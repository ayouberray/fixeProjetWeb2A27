import os
import re

directory = r"c:\xampp\htdocs\Gestion_RDV\projet"

new_nav_template = """<div class="navbar-wrapper">
    <nav class="navbar floating-pill">
        <a href="/projet/index.php" class="nav-logo-link">
            <div class="logo-hybrid">
                <div class="logo-circle"><i class="fas fa-leaf"></i></div>
                <span class="logo-text-serif">InnoGov</span>
            </div>
        </a>
        <div class="nav-menu">
{nav_links}
        </div>
        <div class="nav-actions">
            <div class="lang-switcher-pill">
                <button class="lang-btn active">FR</button>
                <button class="lang-btn">AR</button>
            </div>
            <button class="icon-btn" title="Recherche"><i class="fas fa-search"></i></button>
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="cta-search">Prendre RDV</a>
        </div>
    </nav>
</div>"""

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    # Find <nav class="navbar"> ... </nav>
    nav_pattern = re.compile(r'<nav class="navbar">.*?</nav>', re.DOTALL)
    match = nav_pattern.search(content)
    
    if match:
        old_nav = match.group(0)
        
        # Extract links from inside <div class="nav-menu">
        menu_pattern = re.compile(r'<div class="nav-menu">(.*?)</div>', re.DOTALL)
        menu_match = menu_pattern.search(old_nav)
        
        nav_links = ""
        if menu_match:
            inner_menu = menu_match.group(1)
            # Find all <a> tags that have class="nav-link..."
            link_pattern = re.compile(r'<a[^>]+class="nav-link[^"]*"[^>]*>.*?</a>', re.IGNORECASE)
            links = link_pattern.findall(inner_menu)
            
            nav_links = "\n".join("            " + link for link in links)
        
        new_nav = new_nav_template.replace("{nav_links}", nav_links)
        
        new_content = content.replace(old_nav, new_nav)
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")

for root, dirs, files in os.walk(directory):
    for file in files:
        if file.endswith(".php"):
            process_file(os.path.join(root, file))

print("Done.")
