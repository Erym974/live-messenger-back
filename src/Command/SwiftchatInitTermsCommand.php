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
    name: 'swiftchat:init-terms',
    description: 'Init default terms',
    aliases: ['s:i:t']
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
        
        $terms = [
            [
                'type' => LegalNotice::TERMS,
                'locale' => LegalNotice::ENGLISH,
                'content' => '<h1>SwiftChat - Terms and Conditions</h1><p><br></p><p>By using SwiftChat, you agree to the following terms and conditions:</p><ol><li>Acceptance of Terms:</li><li>By accessing or using SwiftChat, you agree to be bound by these Terms and Conditions, as well as any additional terms and conditions that may apply to specific services offered within SwiftChat.</li><li>User Conduct:</li><li>You agree not to use SwiftChat for any unlawful purpose or in any way that could damage, disable, overburden, or impair the service. You also agree not to use SwiftChat to distribute spam or any other unsolicited communication.</li><li>Privacy:</li><li>Your privacy is important to us. Please review our Privacy Policy to understand how we collect, use, and disclose information about you when you use SwiftChat.</li><li>Intellectual Property:</li><li>All content and materials available on SwiftChat, including but not limited to text, graphics, logos, button icons, images, audio clips, and software, are the property of SwiftChat or its licensors and are protected by copyright, trademark, and other intellectual property laws.</li><li>Disclaimer of Warranties:</li><li>SwiftChat is provided on an "as is" and "as available" basis without warranties of any kind, either express or implied. SwiftChat does not guarantee that the service will be uninterrupted, secure, or error-free.</li><li>Limitation of Liability:</li><li>Under no circumstances shall SwiftChat be liable for any direct, indirect, incidental, special, or consequential damages resulting from the use of or inability to use SwiftChat, including but not limited to damages for loss of profits, goodwill, use, data, or other intangible losses.</li><li>Governing Law:</li><li>These Terms and Conditions shall be governed by and construed in accordance with the laws of [Your Jurisdiction], without regard to its conflict of law provisions.</li><li>Changes to Terms:</li><li>SwiftChat reserves the right to modify or replace these Terms and Conditions at any time. It is your responsibility to review these Terms periodically for changes. Your continued use of SwiftChat after any modifications to these Terms constitutes acceptance of those changes.</li></ol><p><br></p><p>Last Updated: <strong>03/05/2024</strong></p>'
            ],
            [
                'type' => LegalNotice::TERMS,
                'locale' => LegalNotice::FRENCH,
                'content' => '<h1>SwiftChat - Conditions d\'utilisation</h1><p><br></p><p>En utilisant SwiftChat, vous acceptez les conditions suivantes :</p><ol><li>Acceptation des conditions</li><li>En accédant ou en utilisant SwiftChat, vous acceptez d\'être lié par ces Conditions d\'utilisation, ainsi que par toute condition supplémentaire qui pourrait s\'appliquer à des services spécifiques offerts dans SwiftChat.</li><li>Comportement de l\'utilisateur</li><li>Vous acceptez de ne pas utiliser SwiftChat à des fins illégales ou de toute autre manière qui pourrait endommager, désactiver, surcharger ou nuire au service. Vous acceptez également de ne pas utiliser SwiftChat pour distribuer du spam ou toute autre communication non sollicitée.</li><li>Confidentialité</li><li>Votre vie privée est importante pour nous. Veuillez consulter notre Politique de confidentialité pour comprendre comment nous recueillons, utilisons et divulguons des informations vous concernant lorsque vous utilisez SwiftChat.</li><li>Propriété intellectuelle</li><li>Tous les contenus et matériels disponibles sur SwiftChat, y compris mais sans s\'y limiter le texte, les graphiques, les logos, les icônes de boutons, les images, les clips audio et les logiciels, sont la propriété de SwiftChat ou de ses concédants de licence et sont protégés par le droit d\'auteur, les marques de commerce et d\'autres lois sur la propriété intellectuelle.</li><li>Exclusion de garanties</li><li>SwiftChat est fourni "tel quel" et "tel que disponible" sans garanties d\'aucune sorte, qu\'elles soient expresses ou implicites. SwiftChat ne garantit pas que le service sera ininterrompu, sécurisé ou exempt d\'erreurs.</li><li>Limitation de responsabilité</li><li>En aucun cas, SwiftChat ne saurait être tenu responsable de tout dommage direct, indirect, accessoire, spécial ou consécutif résultant de l\'utilisation ou de l\'impossibilité d\'utiliser SwiftChat, y compris mais sans s\'y limiter les dommages pour perte de profits, de réputation, d\'utilisation, de données ou autres pertes intangibles.</li><li>Loi applicable</li><li>Ces Conditions d\'utilisation seront régies par et interprétées conformément aux lois de [Votre Juridiction], sans égard à ses dispositions en matière de conflits de lois.</li><li>Modification des conditions</li><li>SwiftChat se réserve le droit de modifier ou de remplacer ces Conditions d\'utilisation à tout moment. Il est de votre responsabilité de consulter régulièrement ces Conditions pour prendre connaissance des modifications. Votre utilisation continue de SwiftChat après toute modification de ces Conditions constitue votre acceptation de ces changements.</li></ol><p>Dernière mise à jour : <strong>05/03/2024</strong></p>'
            ]
        ];

        foreach($terms as $term) {
            $legalNotice = new LegalNotice();
            $legalNotice->setType($term['type']);
            $legalNotice->setLocale($term['locale']);
            $legalNotice->setContent($term['content']);
            $this->entityManager->persist($legalNotice);
        }

        $this->entityManager->flush();

        $io->success('Default terms has been saved successfully created !');

        return Command::SUCCESS;
    }
}
