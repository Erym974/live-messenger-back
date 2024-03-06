<?php

namespace App\DataFixtures;

use App\Entity\LegalNotice;
use App\Factory\CategoryFactory;
use App\Factory\FriendFactory;
use App\Factory\GroupFactory;
use App\Factory\InvitationFactory;
use App\Factory\JobFactory;
use App\Factory\LegalNoticeFactory;
use App\Factory\MetaFactory;
use App\Factory\PostFactory;
use App\Factory\ProductFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {

        /** Creates Terms & Privacy */
        $this->createTerms();
        $this->createPrivacy();

        /** Create all Metas */
        $this->createMeta();

        /** Admin */
        $admin = UserFactory::createOne([
            'email' => 'admin@admin.fr',
            'firstname' => 'admin',
            'password' => 'admin',
            'roles' => ['ROLE_ADMIN']
        ]);
        
        /** User */
        
        $users = [];
        
        for ($i = 1; $i < 6; $i++) {
            $users[] = UserFactory::createOne([
                'email' => "user-$i@user.fr",
                'firstname' => 'user',
                'lastname' => "$i",
                'password' => 'user'
            ]);
        }


        /** Admin and user 1 */
        $group = $this->createGroup($admin, [$users[0]], true);
        $this->createFriendFor($admin, $users[0], $group);

        /** Admin and User 2 */
        $group = $this->createGroup($admin, [$users[1]], true);
        $this->createFriendFor($admin, $users[1], $group);

        /** User 1 and User 2 */
        $group = $this->createGroup($users[0], [$users[1]], true);
        $this->createFriendFor($users[0], $users[1], $group);

        /** Create group with Admin, User 1 and User 2 */
        $group = $this->createGroup($admin, [$users[0], $users[1]]);

        /** Create Invitation */
        $this->createInvitation($users[2], $admin);


        /** Jobs */
        JobFactory::createMany(10);

        /** Post */
        PostFactory::createMany(10);
        
    }

    private function createFriendFor($user, $friend, $group) : void
    {
        FriendFactory::createOne([
            'conversation' => $group,
            'user' => $user,
            'friend' => $friend
        ]);

        FriendFactory::createOne([
            'conversation' => $group,
            'user' => $friend,
            'friend' => $user
        ]);
    }

    private function createInvitation($emitter, $invitation)
    {

        return InvitationFactory::createOne([
            'emitter' => $emitter,
            'receiver' => $invitation
        ]);

    }

    private function createGroup($admin, $users, $private = false)
    {
        return GroupFactory::createOne([
            'administrator' => $admin,
            'private' => $private,
            'members' => [
                $admin,
                ...$users,
            ]
        ]);
    }

    private function createMeta() {

        MetaFactory::createOne([
            'name' => 'language',
            'value' => 'fr',
            'allowed' => 'string'
        ]);

        MetaFactory::createOne([
            'name' => 'allow-friend-request',
            'value' => 'true',
            'allowed' => 'bool'
        ]);

    }

    private function createTerms() {
        LegalNoticeFactory::createOne([
            'type' => LegalNotice::TERMS,
            'locale' => LegalNotice::FRENCH,
            'content' => '<h1>SwiftChat - Conditions d\'utilisation</h1><p><br></p><p>En utilisant SwiftChat, vous acceptez les conditions suivantes :</p><ol><li>Acceptation des conditions</li><li>En accédant ou en utilisant SwiftChat, vous acceptez d\'être lié par ces Conditions d\'utilisation, ainsi que par toute condition supplémentaire qui pourrait s\'appliquer à des services spécifiques offerts dans SwiftChat.</li><li>Comportement de l\'utilisateur</li><li>Vous acceptez de ne pas utiliser SwiftChat à des fins illégales ou de toute autre manière qui pourrait endommager, désactiver, surcharger ou nuire au service. Vous acceptez également de ne pas utiliser SwiftChat pour distribuer du spam ou toute autre communication non sollicitée.</li><li>Confidentialité</li><li>Votre vie privée est importante pour nous. Veuillez consulter notre Politique de confidentialité pour comprendre comment nous recueillons, utilisons et divulguons des informations vous concernant lorsque vous utilisez SwiftChat.</li><li>Propriété intellectuelle</li><li>Tous les contenus et matériels disponibles sur SwiftChat, y compris mais sans s\'y limiter le texte, les graphiques, les logos, les icônes de boutons, les images, les clips audio et les logiciels, sont la propriété de SwiftChat ou de ses concédants de licence et sont protégés par le droit d\'auteur, les marques de commerce et d\'autres lois sur la propriété intellectuelle.</li><li>Exclusion de garanties</li><li>SwiftChat est fourni "tel quel" et "tel que disponible" sans garanties d\'aucune sorte, qu\'elles soient expresses ou implicites. SwiftChat ne garantit pas que le service sera ininterrompu, sécurisé ou exempt d\'erreurs.</li><li>Limitation de responsabilité</li><li>En aucun cas, SwiftChat ne saurait être tenu responsable de tout dommage direct, indirect, accessoire, spécial ou consécutif résultant de l\'utilisation ou de l\'impossibilité d\'utiliser SwiftChat, y compris mais sans s\'y limiter les dommages pour perte de profits, de réputation, d\'utilisation, de données ou autres pertes intangibles.</li><li>Loi applicable</li><li>Ces Conditions d\'utilisation seront régies par et interprétées conformément aux lois de [Votre Juridiction], sans égard à ses dispositions en matière de conflits de lois.</li><li>Modification des conditions</li><li>SwiftChat se réserve le droit de modifier ou de remplacer ces Conditions d\'utilisation à tout moment. Il est de votre responsabilité de consulter régulièrement ces Conditions pour prendre connaissance des modifications. Votre utilisation continue de SwiftChat après toute modification de ces Conditions constitue votre acceptation de ces changements.</li></ol><p>Dernière mise à jour : <strong>05/03/2024</strong></p>'
        ]);
        
        LegalNoticeFactory::createOne([
            'type' => LegalNotice::TERMS,
            'locale' => LegalNotice::ENGLISH,
            'content' => '<h1>SwiftChat - Terms and Conditions</h1><p><br></p><p>By using SwiftChat, you agree to the following terms and conditions:</p><ol><li>Acceptance of Terms:</li><li>By accessing or using SwiftChat, you agree to be bound by these Terms and Conditions, as well as any additional terms and conditions that may apply to specific services offered within SwiftChat.</li><li>User Conduct:</li><li>You agree not to use SwiftChat for any unlawful purpose or in any way that could damage, disable, overburden, or impair the service. You also agree not to use SwiftChat to distribute spam or any other unsolicited communication.</li><li>Privacy:</li><li>Your privacy is important to us. Please review our Privacy Policy to understand how we collect, use, and disclose information about you when you use SwiftChat.</li><li>Intellectual Property:</li><li>All content and materials available on SwiftChat, including but not limited to text, graphics, logos, button icons, images, audio clips, and software, are the property of SwiftChat or its licensors and are protected by copyright, trademark, and other intellectual property laws.</li><li>Disclaimer of Warranties:</li><li>SwiftChat is provided on an "as is" and "as available" basis without warranties of any kind, either express or implied. SwiftChat does not guarantee that the service will be uninterrupted, secure, or error-free.</li><li>Limitation of Liability:</li><li>Under no circumstances shall SwiftChat be liable for any direct, indirect, incidental, special, or consequential damages resulting from the use of or inability to use SwiftChat, including but not limited to damages for loss of profits, goodwill, use, data, or other intangible losses.</li><li>Governing Law:</li><li>These Terms and Conditions shall be governed by and construed in accordance with the laws of [Your Jurisdiction], without regard to its conflict of law provisions.</li><li>Changes to Terms:</li><li>SwiftChat reserves the right to modify or replace these Terms and Conditions at any time. It is your responsibility to review these Terms periodically for changes. Your continued use of SwiftChat after any modifications to these Terms constitutes acceptance of those changes.</li></ol><p><br></p><p>Last Updated: <strong>03/05/2024</strong></p>'
        ]);
    }

    private function createPrivacy() {
        LegalNoticeFactory::createOne([
            'type' => LegalNotice::PRIVACY,
            'locale' => LegalNotice::FRENCH,
            'content' => '<h1>Politique de Confidentialité de SwiftChat</h1><p><br></p><p>Dernière mise à jour : <strong>05/03/2024</strong></p><p><br></p><p><strong>SwiftChat </strong>accorde une grande importance à la confidentialité de ses utilisateurs. Cette Politique de Confidentialité décrit comment nous recueillons, utilisons et protégeons les informations que vous nous fournissez lorsque vous utilisez notre application.</p><p><br></p><ol><li>Collecte et Utilisation des Informations</li></ol><p>Nous ne collectons aucune information personnelle identifiable à moins que vous ne choisissiez de nous fournir ces informations volontairement, par exemple en vous connectant à l\napplication <strong>SwiftChat</strong>.</p><p><strong>SwiftChat </strong>utilise des cookies pour permettre votre connexion à l\napplication. Ces cookies sont stockés localement sur votre appareil et ne sont pas partagés avec des tiers.</p><ol><li>Protection des Données</li></ol><p>Nous prenons des mesures de sécurité appropriées pour protéger vos informations contre tout accès non autorisé, altération, divulgation ou destruction. Vos informations de connexion sont stockées de manière sécurisée sur nos serveurs et ne sont accessibles qu\naux employés autorisés de <strong>SwiftChat </strong>dans le cadre de leurs fonctions.</p><ol><li>Partage des Informations</li></ol><p>Nous ne partageons pas vos informations personnelles avec des tiers, sauf dans les circonstances suivantes :</p><ul><li>Si cela est nécessaire pour se conformer à la loi, à une ordonnance judiciaire ou à une procédure judiciaire.</li><li>Pour protéger les droits, la propriété ou la sécurité de <strong>SwiftChat</strong>, de nos utilisateurs ou du public.</li><li>En cas de fusion, acquisition ou vente d\nactifs de <strong>SwiftChat</strong>, auquel cas les utilisateurs seraient informés par le biais d\nun avis sur notre site Web ou par courrier électronique.</li></ul><ol><li>Modifications de la Politique de Confidentialité</li></ol><p><strong>SwiftChat </strong>se réserve le droit de modifier cette Politique de Confidentialité à tout moment. Toute modification sera publiée sur cette page avec une mise à jour de la date de dernière révision. Nous vous encourageons à consulter régulièrement cette page pour rester informé des modifications apportées à notre Politique de Confidentialité.</p><p>En utilisant <strong>SwiftChat</strong>, vous acceptez cette Politique de Confidentialité. Si vous avez des questions ou des préoccupations concernant cette Politique de Confidentialité, veuillez nous contacter à l\nadresse contact@swiftchat.fr.</p><p><br></p><p>Merci d\nutiliser <strong>SwiftChat</strong>.</p>'
        ]);

        LegalNoticeFactory::createOne([
            'type' => LegalNotice::PRIVACY,
            'locale' => LegalNotice::ENGLISH,
            'content' => '<h1><strong>Privacy Policy for SwiftChat</strong></h1><p><br></p><p>Last Updated: <strong>03/05/2024</strong></p><p><br></p><p><strong>SwiftChat </strong>values the privacy of its users. This Privacy Policy outlines how we collect, use, and safeguard the information you provide to us when using our application.</p><ol><li>Collection and Use of Information</li></ol><p>We do not collect any personally identifiable information unless you choose to provide such information voluntarily, such as when logging into the <strong>SwiftChat </strong>application.</p><p><strong>SwiftChat </strong>uses cookies to enable your login to the application. These cookies are stored locally on your device and are not shared with third parties.</p><ol><li>Data Protection</li></ol><p>We take appropriate security measures to protect your information from unauthorized access, alteration, disclosure, or destruction. Your login information is securely stored on our servers and is only accessible to authorized <strong>SwiftChat </strong>employees in the course of their duties.</p><ol><li>Sharing of Information</li></ol><p>We do not share your personal information with third parties, except under the following circumstances:</p><ul><li>If necessary to comply with the law, a court order, or legal process.</li><li>To protect the rights, property, or safety of <strong>SwiftChat</strong>, our users, or the public.</li><li>In the event of a merger, acquisition, or sale of <strong>SwiftChat </strong>assets, in which case users would be notified via notice on our website or by email.</li></ul><ol><li>Changes to the Privacy Policy</li></ol><p><strong>SwiftChat </strong>reserves the right to modify this Privacy Policy at any time. Any changes will be posted on this page along with an update to the last revision date. We encourage you to check this page regularly to stay informed of any changes to our Privacy Policy.</p><p>By using <strong>SwiftChat</strong>, you agree to this Privacy Policy. If you have any questions or concerns about this Privacy Policy, please contact us at contact@swiftchat.fr.</p><p><br></p><p>Thank you for using <strong>SwiftChat</strong>.</p>'
        ]);
    }
}
