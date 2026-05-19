# app.py — Lumière Studio
# Lancez avec : streamlit run app.py

import streamlit as st

# — Configuration de la page
st.set_page_config(
    page_title="Lumière Studio",
    page_icon="💡",
    layout="wide"
)

# — CSS personnalisé
st.markdown(
    """
    <style>
    /* Fond crème */
    #app { background-color: #f5f0e8; }

    /* Cacher le menu streamlit */
    #MainMenu, footer, header { visibility: hidden; }

    /* Navigation */
    .nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background: rgba(245,240,232,0.95);
    }

    .nav-logo {
        font-size: 1.4rem;
        font-weight: bold;
        color: #e00e0e;
    }

    .nav-logo span { color: #9a8a4c; }

    /* Hero */
    .hero {
        padding: 5rem 4rem 4rem 4rem;
        max-width: 700px;
    }

    .hero-tag {
        font-size: 0.75rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #9a8a4c;
        border-bottom: 1px solid #9a8a4c;
        padding-bottom: 4px;
        display: inline-block;
        margin-bottom: 1.5rem;
    }

    .hero h1 {
        font-size: 3rem;
        color: #e00e0e;
    }

    .hero h1 em { color: #a9844c; font-style: italic; }
    .hero p { color: #b6b6b6; font-size: 1.05rem; }

    /* section services */
    .services-bg {
        background: #0e0e0e;
        padding: 4rem 2rem;
        margin-top: 2rem;
    }

    .services-bg h2 {
        color: #f5f6e8;
        font-size: 2em;
        margin-bottom: 0.5rem;
    }

    .services-bg .label {
        color: #a9844c;
        font-size: 0.75rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
    }

    /* cards services */
    .card {
        background: #151616;
        border: 1px solid #2a2a2a;
        border-radius: 4px;
        padding: 1.8rem;
    }

    .card-num {
        font-size: 2.5rem;
        color: #c394ac;
        opacity: 0.35;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 0.8rem;
    }

    .card h3 { color: #f1508e; margin-bottom: 0.6rem; }
    .card p { color: #888; font-size: 0.9rem; }
    </style>
    """,
    unsafe_allow_html=True
)

# Navigation
st.markdown("""
<div class="nav">
    <div class="nav-logo"><span>Juniée</span>Studio</div>
    <div style="display:flex; gap:2rem; color:#6b6b6b; font-size:0.9rem;">
        <span>Services</span>
        <span>A propos</span>
        <span style="background:#90e0ec;color:#f1508e;padding:0.4rem 1rem;border-radius:2px">Contact</span>
    </div>
</div>
""", unsafe_allow_html=True)
# Hero
st.markdown("""
<div class="hero">
<div class="hero-tag">Agence créative – Paris</div>
<h1>Nous donnons vie à <em>vos idées</em><br>les plus audacieuses.</h1>
<p>Design, développement web et stratégie digitale<br>
pour des marques qui osent se démarquer.</p>
</div>
""", unsafe_allow_html=True)

# Services
st.markdown("""
<div class="label">Ce que nous faisons</div>
<h2>Nos domaines d'expertises</h2>
""", unsafe_allow_html=True)

col1, col2, col3 = st.columns(3)

with col1:
    st.markdown("""
    <div class="card">
        <h3>Design UI/UX</h3>
        <p>Interfaces intuitives et expériences utilisateur mémorables.</p>
    </div>
    """, unsafe_allow_html=True)

with col2:
    st.markdown("""
    <div class="card">
        <div class="card-num">02</div>
        <h3>Développement Web</h3>
        <p>Sites vitrines et applications avec les technologies actuelles.</p>
    </div>
    """, unsafe_allow_html=True)

with col3:
    st.markdown("""
        <div class="card">
            <div class="card-num">03</div>
            <h3>Identité de marque</h3>
            <p>Logotypes et chartes graphiques qui incarnent vos valeurs.</p>
        </div>
    """, unsafe_allow_html=True)


# --- Contact ---
st.markdown("<br><br>", unsafe_allow_html=True)
st.markdown("## 📬 Contactez-nous!")

col_a, col_b = st.columns([1, 2])

with col_a:
    st.markdown("### 💬 Parlez-nous de votre projet, nous sommes à l'écoute.")
    st.markdown("📧 bonjour@lumiere.studio")
    st.markdown("📍 12 rue du Faubourg, 75011 Paris")

with col_b:
    with st.form("contact_form"):
        prenom = st.text_input("Prénom")
        email = st.text_input("Email")
        sujet = st.selectbox("Sujet", [
            "Création de site web",
            "Design d'identité visuelle",
            "Refonte de site existant",
            "Autre demande"
        ])
        message = st.text_area("Votre message")
        envoyer = st.form_submit_button("Envoyer le message")

if envoyer:
    if prenom and email and message:
        st.success(f"Merci {prenom} ! Votre message a bien été envoyé.")
    else:
        st.warning("Merci de remplir tous les champs.")

# Footer
st.markdown(
    "<p style='text-align:center;color:#aaa;font-size:0.8em;'>"
    "© 2025 Lumière Studio — Tous droits réservés</p>",
    unsafe_allow_html=True
)


