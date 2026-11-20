# Chic Dressing — optimisation d'un site WordPress existant

Blog et boutique WooCommerce de vêtements de luxe de seconde main (cliente fictive).
Le site était lent, mal référencé et peu accessible : je l'ai optimisé sans le reconstruire.

**Démo :** [stephanietrnc.github.io/chic-dressing](https://stephanietrnc.github.io/chic-dressing/)
*(version statique : le panier et la commande ne fonctionnent pas dans la démo)*

## Contexte

Projet de la formation Développeur WordPress d'OpenClassrooms, repris et poussé plus loin ensuite.
Point de départ : un thème gratuit (Ashe) modifié sans méthode, des images de plus de 5 000 px,
plusieurs H1, aucune extension SEO, des thèmes et extensions inutiles.

## Résultats (Google Lighthouse, page d'accueil)

| | Desktop | Mobile |
|---|---|---|
| Site reçu | 66 | 54 |
| Après le projet de formation | 97 | 61 |
| Après la reprise | 100 | 90 |

Mobile : LCP de 49,5 s à 3,5 s environ. Accessibilité de 88 à 96. SEO 100 (hors démo, qui est volontairement non indexée).
*Mesures faites en local avec Lighthouse ; score mobile = médiane de 3 mesures (90 à 91).*

## Ce que j'ai fait pendant la formation

- Images compressées, redimensionnées et converties en WebP (Imagify)
- Polices hébergées sur le site au lieu de Google Fonts
- Chargement différé de la carte Google Maps et du widget Twitter
- Cache et minification (W3 Total Cache)
- SEO : un seul H1, hiérarchie des titres corrigée, méta-description et canonical (Rank Math), textes alternatifs
- Contrastes renforcés, thèmes inutiles supprimés, PHP mis à jour, HTTPS

## Ce que j'ai ajouté ensuite

- Slider : vraies images (`<img>` + `srcset`) au lieu de fonds CSS, la première chargée en priorité
- Image d'en-tête à la bonne taille selon l'écran, et préchargée
- Photos produits nettes, en vignette comme sur la page produit (bonne taille annoncée au navigateur), vignettes chargées en différé
- Font Awesome réduit aux icônes utilisées : 1 Ko au lieu de 250 Ko (CSS + police)
- CSS combiné : 19 fichiers bloquants ramenés à 3, et CSS inutile retiré de l'accueil (boutons de partage, blocs WooCommerce) : environ 100 Ko en moins
- Script des émojis WordPress retiré
- Accessibilité : repère `<main>`, liens sans nom corrigés, liste HTML valide
- Responsive : grille des produits sur mobile, espacements réguliers entre les sections
- Liens corrigés : « Voir tous les articles » menait à une page inexistante, « Le blog » renvoie à la section des articles
- Contenu de démo du thème nettoyé : galerie aux images manquantes, image chargée depuis un site externe, code de formulaire affiché sur la page Contact
- Formulaires et métadonnées : case à cocher des commentaires et nom de l'auteur alignés (une largeur minimale de 320 px les décalait)

## Structure

- `themes/chicdressing` : thème enfant de Ashe, avec toutes les modifications
- `docs` : version statique du site, publiée avec GitHub Pages

## Lancer le projet en local

1. Installer WordPress (par exemple avec Local), WooCommerce et le thème Ashe.
2. Copier `themes/chicdressing` dans `wp-content/themes`.
3. Activer le thème `chicdressing`.
