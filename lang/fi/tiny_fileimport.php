<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'tiny_fileimport', language 'fi'.
 *
 * @package    tiny_fileimport
 * @author     Mikko Haiku
 * @copyright  2026 Finspire <info@finspi.re>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Tiedostojen tuonti';
$string['buttontitle'] = 'Tiedostojen tuonti';
$string['modaltitle'] = 'Lisää tiedostoja';
$string['dropzonehint'] = 'Vedä ja pudota tiedostoja tähän tai valitse tiedostot napsauttamalla';
$string['allowalltypes'] = 'Salli kaikki tiedostotyypit';
$string['allowalltypes_desc'] = 'Jos käytössä, latauksia ei rajoiteta sivuston hallinnon > Palvelin > Tiedostotyypit -osiossa lueteltuihin tiedostotyyppeihin. Jos poistettu käytöstä, kaikki siellä luetellut tiedostotyypit sallitaan oletuksena.';
$string['overridedefaultfileattachmentfeature'] = 'Ohita oletusarvoinen tiedostojen liittämistoiminto';
$string['overridedefaultfileattachmentfeature_desc'] = 'Jos käytössä, tämä lisäosa käsittelee editorin vedä ja pudota -lataukset Tiny:n oletusarvoisen tiedostojen liittämiskäsittelyn sijaan. Jos pois käytöstä, editori säilyttää oman oletuslatauksensa tuetuille tiedostoille, kuten kuville, ja tätä lisäosaa käytetään vain tiedostoille, joita editorin oletuslataus ei käsittele.';
$string['allowedextensionsoverride'] = 'Sallittujen tiedostopäätteiden ohitus';
$string['allowedextensionsoverride_desc'] = 'Valinnainen. Pilkulla, välilyönnillä tai rivinvaihdolla erotettu luettelo sallituista tiedostopäätteistä (esimerkiksi: pdf, docx, xlsx, zip). Jos tyhjä, lisäosa käyttää täydellistä luetteloa sivuston hallinnosta > Palvelin > Tiedostotyypit. Ohitetaan, kun "Salli kaikki tiedostotyypit" on käytössä.';
$string['filetypenotsupported'] = 'Tiedostotyyppi ei ole tuettu';
$string['filetypenotsupported_desc'] = 'Tiedostoa "{$a}" ei voitu ladata, koska sen tiedostotyyppi ei ole tuettu nykyisten asetusten mukaan.';
$string['fileimport:use'] = 'Käytä Tiny-tiedostojen tuontia';
$string['privacy:metadata'] = 'Tiny-tiedostojen tuonti -lisäosa ei tallenna henkilötietoja.';
