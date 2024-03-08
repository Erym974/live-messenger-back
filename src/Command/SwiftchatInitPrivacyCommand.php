<?php

namespace App\Command;

use App\Entity\LegalNotice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'swiftchat:init:privacy',
    description: 'Init default privacy',
    aliases: ['s:i:p']
)]
class SwiftchatInitTermsCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $privacy = [
            [
                'type' => LegalNotice::PRIVACY,
                'locale' => LegalNotice::FRENCH,
                'content' => '<h1>Politique de Confidentialité de SwiftChat</h1><p><br></p><p>Dernière mise à jour : <strong>05/03/2024</strong></p><p><br></p><p><strong>SwiftChat </strong>accorde une grande importance à la confidentialité de ses utilisateurs. Cette Politique de Confidentialité décrit comment nous recueillons, utilisons et protégeons les informations que vous nous fournissez lorsque vous utilisez notre application.</p><p><br></p><ol><li>Collecte et Utilisation des Informations</li></ol><p>Nous ne collectons aucune information personnelle identifiable à moins que vous ne choisissiez de nous fournir ces informations volontairement, par exemple en vous connectant à l\napplication <strong>SwiftChat</strong>.</p><p><strong>SwiftChat </strong>utilise des cookies pour permettre votre connexion à l\napplication. Ces cookies sont stockés localement sur votre appareil et ne sont pas partagés avec des tiers.</p><ol><li>Protection des Données</li></ol><p>Nous prenons des mesures de sécurité appropriées pour protéger vos informations contre tout accès non autorisé, altération, divulgation ou destruction. Vos informations de connexion sont stockées de manière sécurisée sur nos serveurs et ne sont accessibles qu\naux employés autorisés de <strong>SwiftChat </strong>dans le cadre de leurs fonctions.</p><ol><li>Partage des Informations</li></ol><p>Nous ne partageons pas vos informations personnelles avec des tiers, sauf dans les circonstances suivantes :</p><ul><li>Si cela est nécessaire pour se conformer à la loi, à une ordonnance judiciaire ou à une procédure judiciaire.</li><li>Pour protéger les droits, la propriété ou la sécurité de <strong>SwiftChat</strong>, de nos utilisateurs ou du public.</li><li>En cas de fusion, acquisition ou vente d\nactifs de <strong>SwiftChat</strong>, auquel cas les utilisateurs seraient informés par le biais d\nun avis sur notre site Web ou par courrier électronique.</li></ul><ol><li>Modifications de la Politique de Confidentialité</li></ol><p><strong>SwiftChat </strong>se réserve le droit de modifier cette Politique de Confidentialité à tout moment. Toute modification sera publiée sur cette page avec une mise à jour de la date de dernière révision. Nous vous encourageons à consulter régulièrement cette page pour rester informé des modifications apportées à notre Politique de Confidentialité.</p><p>En utilisant <strong>SwiftChat</strong>, vous acceptez cette Politique de Confidentialité. Si vous avez des questions ou des préoccupations concernant cette Politique de Confidentialité, veuillez nous contacter à l\nadresse contact@swiftchat.fr.</p><p><br></p><p>Merci d\nutiliser <strong>SwiftChat</strong>.</p>'
            ],
            [
                'type' => LegalNotice::PRIVACY,
                'locale' => LegalNotice::ENGLISH,
                'content' => '<h1><strong>Privacy Policy for SwiftChat</strong></h1><p><br></p><p>Last Updated: <strong>03/05/2024</strong></p><p><br></p><p><strong>SwiftChat </strong>values the privacy of its users. This Privacy Policy outlines how we collect, use, and safeguard the information you provide to us when using our application.</p><ol><li>Collection and Use of Information</li></ol><p>We do not collect any personally identifiable information unless you choose to provide such information voluntarily, such as when logging into the <strong>SwiftChat </strong>application.</p><p><strong>SwiftChat </strong>uses cookies to enable your login to the application. These cookies are stored locally on your device and are not shared with third parties.</p><ol><li>Data Protection</li></ol><p>We take appropriate security measures to protect your information from unauthorized access, alteration, disclosure, or destruction. Your login information is securely stored on our servers and is only accessible to authorized <strong>SwiftChat </strong>employees in the course of their duties.</p><ol><li>Sharing of Information</li></ol><p>We do not share your personal information with third parties, except under the following circumstances:</p><ul><li>If necessary to comply with the law, a court order, or legal process.</li><li>To protect the rights, property, or safety of <strong>SwiftChat</strong>, our users, or the public.</li><li>In the event of a merger, acquisition, or sale of <strong>SwiftChat </strong>assets, in which case users would be notified via notice on our website or by email.</li></ul><ol><li>Changes to the Privacy Policy</li></ol><p><strong>SwiftChat </strong>reserves the right to modify this Privacy Policy at any time. Any changes will be posted on this page along with an update to the last revision date. We encourage you to check this page regularly to stay informed of any changes to our Privacy Policy.</p><p>By using <strong>SwiftChat</strong>, you agree to this Privacy Policy. If you have any questions or concerns about this Privacy Policy, please contact us at contact@swiftchat.fr.</p><p><br></p><p>Thank you for using <strong>SwiftChat</strong>.</p>'
            ]
        ];

        foreach($privacy as $term) {
            $legalNotice = new LegalNotice();
            $legalNotice->setType($term['type']);
            $legalNotice->setLocale($term['locale']);
            $legalNotice->setContent($term['content']);
            $this->entityManager->persist($legalNotice);
        }
        $this->entityManager->flush();

        $io->success('Default privacy has been saved successfully created !');

        return Command::SUCCESS;
    }
}
