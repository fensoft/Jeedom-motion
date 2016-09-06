<?php
require_once '/usr/share/nginx/www/jeedom/core/php/core.inc.php';
$file='/etc/motion/motion.conf';
$fp = fopen($file,"w+");
$Config='# Démarrer le daemon (tâche de fond) et redonner la main sur le terminal
daemon on
control_html_output on
control_port '.config::byKey('Port', 'motion').'
# Largeur de l\'image capturée (pixel). Dépend de la caméra utilisée
width '.config::byKey('width', 'motion').'
# Hauteur de l\'image capturée (pixel).
height '.config::byKey('height', 'motion').'
# Nombre d\'images capturées par seconde. Plus ce nombre est grand plus l\'image sera fluide, mais plus la taille prise par les enregistrements sera grande et votre débit (dans le cas d\'une utilisation sur internet) devra être important.
framerate '.config::byKey('framerate', 'motion').'
# Règle la sensibilité de la détection de mouvement: nombre de pixels qui doivent changer entre deux images (plus la valeur est faible plus la détection sera sensible)
threshold '.config::byKey('threshold', 'motion').'
# Utiliser ffmpeg pour encoder les vidéos mpeg en temps réel
ffmpeg_cap_new '.config::byKey('ffmpeg_cap_new', 'motion').'
# Format des vidéos enregistrées. Le mpeg4 produira des fichiers .avi mais d\'autres formats sont disponibles.
ffmpeg_video_codec '.config::byKey('ffmpeg_video_codec', 'motion').'
#Liste de camera de surveillance ';	

fputs($fp,$Config);
fclose($fp);
?>