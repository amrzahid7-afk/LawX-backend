<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NotificationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('notifications')->delete();
        
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => '03f3579c-59de-4107-986a-8dfa8dfa7679',
                'type' => 'App\\Notifications\\RequestSignatureNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"request_signature_id":"01a02b77-70a3-7291-94f3-ca129a25722b","Document_id":"e68c390f-b2ce-4f4a-bd84-4316636e3337","message":"You have received a new Document To sign It Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 21:54:14',
                'updated_at' => '2026-08-22 21:54:14',
            ),
            1 => 
            array (
                'id' => '0b1f24b5-3179-4c74-998f-799929d25414',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"request_id":"82209c88-62b8-484f-b0fc-5b2ed52b76fb","title":"\\u062f\\u0639\\u0648\\u0649 \\u062d\\u0635\\u0631 \\u0627\\u0631\\u062b","message":"New request: \\u062f\\u0639\\u0648\\u0649 \\u062d\\u0635\\u0631 \\u0627\\u0631\\u062b"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:45:49',
                'updated_at' => '2026-08-22 09:45:49',
            ),
            2 => 
            array (
                'id' => '0d0d889d-241a-4faa-9862-44c24b917f71',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f7bf797d-8c4c-4e7b-871f-1e56ebc7c085',
                'data' => '{"request_id":"2e4dd051-fc1d-45c8-b867-cfe6ccfbfab6","title":"\\u062f\\u0639\\u0648\\u0649 \\u062a\\u0641\\u0631\\u064a\\u0642","message":"New request: \\u062f\\u0639\\u0648\\u0649 \\u062a\\u0641\\u0631\\u064a\\u0642"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:43:39',
                'updated_at' => '2026-08-22 09:43:39',
            ),
            3 => 
            array (
                'id' => '0d99fc28-da5c-483e-87ce-e923f7ddb111',
                'type' => 'App\\Notifications\\RequestSignatureNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '584d073b-a517-45ac-b79d-82f047e9ab3b',
                'data' => '{"request_signature_id":"01a02b77-70a3-7291-94f3-ca129a25722b","Document_id":"e68c390f-b2ce-4f4a-bd84-4316636e3337","message":"You have received a new Document To sign It Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 21:54:14',
                'updated_at' => '2026-08-22 21:54:14',
            ),
            4 => 
            array (
                'id' => '20f238be-000d-42fc-bb09-47b7995e986b',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"request_id":"9ce6fabe-1d7a-4057-87b6-7ba18418b733","title":"\\u0627\\u0633\\u062a\\u062e\\u0631\\u0627\\u062c \\u0631\\u062e\\u0635\\u0629 \\u0633\\u062c\\u0644 \\u062a\\u062c\\u0627\\u0631\\u064a","message":"New request: \\u0627\\u0633\\u062a\\u062e\\u0631\\u0627\\u062c \\u0631\\u062e\\u0635\\u0629 \\u0633\\u062c\\u0644 \\u062a\\u062c\\u0627\\u0631\\u064a"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:32:47',
                'updated_at' => '2026-08-22 09:32:47',
            ),
            5 => 
            array (
                'id' => '307e2320-da55-4c72-b698-1bbf9f56611d',
                'type' => 'App\\Notifications\\Add_Session_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'data' => '{"session_id":"eb76d49d-e8a8-43f6-a174-495b29d3c8f9","message":"You have A new Seesion"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:57:37',
                'updated_at' => '2026-08-22 20:57:37',
            ),
            6 => 
            array (
                'id' => '3761015e-e909-4106-9797-751038b4de53',
                'type' => 'App\\Notifications\\Send_Proposal_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"proposal_id":"4975e8f9-25ff-476e-b15d-539342a5f662","message":"You have received a new proposal. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 10:09:55',
                'updated_at' => '2026-08-22 10:09:55',
            ),
            7 => 
            array (
                'id' => '3a16018e-8aaa-4905-bb72-6cb698f2d773',
                'type' => 'App\\Notifications\\Send_Proposal_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"proposal_id":"06144483-ffef-41d1-b9ee-f79865badb38","message":"You have received a new proposal. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 10:14:52',
                'updated_at' => '2026-08-22 10:14:52',
            ),
            8 => 
            array (
                'id' => '40c65d00-6f40-48fe-8d05-7f9ee715eaaa',
                'type' => 'App\\Notifications\\Send_Proposal_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '584d073b-a517-45ac-b79d-82f047e9ab3b',
                'data' => '{"proposal_id":"7c5d31e5-8d75-4c53-b981-fba60bc1927c","message":"You have received a new proposal. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 10:03:34',
                'updated_at' => '2026-08-22 10:03:34',
            ),
            9 => 
            array (
                'id' => '4404d28e-a8e7-4e43-a40d-1da7b24e714b',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"request_id":"d1aa47d4-549c-45a9-9b50-36b310099b43","title":"\\u0627\\u0633\\u062a\\u062e\\u0631\\u0627\\u062c \\u0631\\u062e\\u0635\\u0629 \\u0633\\u062c\\u0644 \\u062a\\u062c\\u0627\\u0631\\u064a","message":"New request: \\u0627\\u0633\\u062a\\u062e\\u0631\\u0627\\u062c \\u0631\\u062e\\u0635\\u0629 \\u0633\\u062c\\u0644 \\u062a\\u062c\\u0627\\u0631\\u064a"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:33:34',
                'updated_at' => '2026-08-22 09:33:34',
            ),
            10 => 
            array (
                'id' => '45eeea19-bb4f-4c70-8109-c408b28d460f',
                'type' => 'App\\Notifications\\Add_Session_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"session_id":"505bcb42-2bd0-473c-af27-007458086b20","message":"You have A new Seesion"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:55:53',
                'updated_at' => '2026-08-22 20:55:53',
            ),
            11 => 
            array (
                'id' => '54c3a59f-8417-40f0-916e-6189b5a9c58c',
                'type' => 'App\\Notifications\\Add_Task_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"task_id":"8cf94ad7-69ac-4ef7-a898-c67cd13eb50e","message":"You have A new task"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:50:06',
                'updated_at' => '2026-08-22 20:50:06',
            ),
            12 => 
            array (
                'id' => '5b76cab1-b55b-4e8a-b978-8cb51a8467de',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"request_id":"3ed93eec-e922-4215-a209-63956adcb193","title":"\\u062a\\u062b\\u0628\\u064a\\u062a \\u0628\\u064a\\u0639","message":"New request: \\u062a\\u062b\\u0628\\u064a\\u062a \\u0628\\u064a\\u0639"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:34:08',
                'updated_at' => '2026-08-22 09:34:08',
            ),
            13 => 
            array (
                'id' => '616d317b-8c39-42e5-8b7e-db7b50795109',
                'type' => 'App\\Notifications\\Add_Task_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"task_id":"d7846dbe-ff88-4165-96b3-19248575be03","message":"You have A new task"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:49:06',
                'updated_at' => '2026-08-22 20:49:06',
            ),
            14 => 
            array (
                'id' => '8d006b5e-cf5a-474c-86fe-c0582d268c69',
                'type' => 'App\\Notifications\\Add_Task_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"task_id":"15992d96-0c05-4b00-b815-2cc9095385dc","message":"You have A new task"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:47:02',
                'updated_at' => '2026-08-22 20:47:02',
            ),
            15 => 
            array (
                'id' => '8d9aee58-ea20-4fc5-9d74-13369cfd537a',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"request_id":"06f5af97-8867-41b3-a524-63850bfe7bbc","title":"\\u062f\\u0639\\u0648\\u0649 \\u0646\\u0641\\u0642\\u0629","message":"New request: \\u062f\\u0639\\u0648\\u0649 \\u0646\\u0641\\u0642\\u0629"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:46:50',
                'updated_at' => '2026-08-22 09:46:50',
            ),
            16 => 
            array (
                'id' => '8f163b69-f714-4f26-9717-56f9c560600b',
                'type' => 'App\\Notifications\\Add_Task_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"task_id":"1e10b057-241b-4afc-91af-f75aaad60c08","message":"You have A new task"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:48:07',
                'updated_at' => '2026-08-22 20:48:07',
            ),
            17 => 
            array (
                'id' => '92cfe3b5-aeee-44ad-87a0-ac305008513c',
                'type' => 'App\\Notifications\\Add_Task_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"task_id":"9ab17804-0cc3-4068-951c-ef088f0f18f4","message":"You have A new task"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:47:42',
                'updated_at' => '2026-08-22 20:47:42',
            ),
            18 => 
            array (
                'id' => '9fd71a88-a397-4eb7-ba56-336ab9b66701',
                'type' => 'App\\Notifications\\Create_Invoice_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'data' => '{"invoice_id":"841f5ed3-19e5-43c3-8fbb-8f0322ed826e","message":"You have received a new invoice. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 21:01:32',
                'updated_at' => '2026-08-22 21:01:32',
            ),
            19 => 
            array (
                'id' => 'a416a4c4-fb37-4eda-a2be-11350034de77',
                'type' => 'App\\Notifications\\Send_Proposal_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'data' => '{"proposal_id":"1f364714-36e0-4729-bee7-742d9eaf6983","message":"You have received a new proposal. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 10:04:16',
                'updated_at' => '2026-08-22 10:04:16',
            ),
            20 => 
            array (
                'id' => 'a701ab5a-dc88-482d-95ba-b788ac7d87e5',
                'type' => 'App\\Notifications\\Create_Invoice_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'data' => '{"invoice_id":"0bfb9b4d-3f98-4f82-887d-51a6ee3efc82","message":"You have received a new invoice. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 21:04:10',
                'updated_at' => '2026-08-22 21:04:10',
            ),
            21 => 
            array (
                'id' => 'a71268b0-38dc-4dd0-b332-61fb3f8385b9',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"request_id":"ab840341-50d0-4161-b0bd-b11de6b3f58a","title":"\\u062f\\u0639\\u0648\\u0649 \\u062a\\u062b\\u0628\\u064a\\u062a \\u0632\\u0648\\u0627\\u062c","message":"New request: \\u062f\\u0639\\u0648\\u0649 \\u062a\\u062b\\u0628\\u064a\\u062a \\u0632\\u0648\\u0627\\u062c"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:46:17',
                'updated_at' => '2026-08-22 09:46:17',
            ),
            22 => 
            array (
                'id' => 'b47a7a7b-1233-41db-84a7-b0d7b0c0b065',
                'type' => 'App\\Notifications\\Create_Invoice_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'data' => '{"invoice_id":"fe2d623d-824e-4932-8048-618663644e28","message":"You have received a new invoice. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 21:04:20',
                'updated_at' => '2026-08-22 21:04:20',
            ),
            23 => 
            array (
                'id' => 'bbc0451d-1d87-4e0c-a22c-45a55859100a',
                'type' => 'App\\Notifications\\Send_Proposal_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"proposal_id":"3e82deb1-e3db-43cc-aedd-207f4e6efd22","message":"You have received a new proposal. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 10:03:58',
                'updated_at' => '2026-08-22 10:03:58',
            ),
            24 => 
            array (
                'id' => 'bee2f4a4-82b3-4a1c-aad5-302b3e93102d',
                'type' => 'App\\Notifications\\CaseClosedNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"case_id":"4f506dd8-8c11-46db-b6c1-0f8ebe97c295","case_title":"\\u0645\\u0639\\u0627\\u0645\\u0644\\u0629 \\u0633\\u062c\\u0644 \\u062a\\u062c\\u0627\\u0631\\u064a","message":"\\u062a\\u0645 \\u0625\\u063a\\u0644\\u0627\\u0642 \\u0642\\u0636\\u064a\\u062a\\u0643 \\u0628\\u0646\\u062c\\u0627\\u062d\\u060c \\u064a\\u0631\\u062c\\u0649 \\u062a\\u0642\\u064a\\u064a\\u0645 \\u0627\\u0644\\u0645\\u062d\\u0627\\u0645\\u064a.","type":"case_closed"}',
                'read_at' => NULL,
                'created_at' => '2026-08-23 00:29:35',
                'updated_at' => '2026-08-23 00:29:35',
            ),
            25 => 
            array (
                'id' => 'c3fb11d1-3a1e-4ffb-afd7-04eacb4886a9',
                'type' => 'App\\Notifications\\Send_Proposal_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"proposal_id":"cf402f8b-c66b-4193-a598-4fd2cca64936","message":"You have received a new proposal. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 10:03:14',
                'updated_at' => '2026-08-22 10:03:14',
            ),
            26 => 
            array (
                'id' => 'c6d67c6d-633f-4255-88a0-e9a7ff2ad258',
                'type' => 'App\\Notifications\\Add_Session_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'data' => '{"session_id":"d939e12f-04c4-467d-a5b7-98df6829341d","message":"You have A new Seesion"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:56:33',
                'updated_at' => '2026-08-22 20:56:33',
            ),
            27 => 
            array (
                'id' => 'c6eaa5a7-23d9-457e-af0f-7a5043630ad2',
                'type' => 'App\\Notifications\\Add_Task_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"task_id":"fbb2ec66-d558-4130-954a-753986e56643","message":"You have A new task"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:46:11',
                'updated_at' => '2026-08-22 20:46:11',
            ),
            28 => 
            array (
                'id' => 'c77ba853-5b4f-47c4-bb3b-4944c6f55925',
                'type' => 'App\\Notifications\\Send_Proposal_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"proposal_id":"dbffe769-49a4-4da2-bfb6-9e37789917e5","message":"You have received a new proposal. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 10:15:10',
                'updated_at' => '2026-08-22 10:15:10',
            ),
            29 => 
            array (
                'id' => 'c8ad7d20-e074-47d8-8217-26b51f2dd763',
                'type' => 'App\\Notifications\\Add_Session_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'data' => '{"session_id":"505bcb42-2bd0-473c-af27-007458086b20","message":"You have A new Seesion"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:55:53',
                'updated_at' => '2026-08-22 20:55:53',
            ),
            30 => 
            array (
                'id' => 'ccb49624-8ba8-4423-a31d-23926bc37512',
                'type' => 'App\\Notifications\\CaseClosedNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'data' => '{"case_id":"4f506dd8-8c11-46db-b6c1-0f8ebe97c295","case_title":"\\u0645\\u0639\\u0627\\u0645\\u0644\\u0629 \\u0633\\u062c\\u0644 \\u062a\\u062c\\u0627\\u0631\\u064a","message":"\\u062a\\u0645 \\u0625\\u063a\\u0644\\u0627\\u0642 \\u0642\\u0636\\u064a\\u062a\\u0643 \\u0628\\u0646\\u062c\\u0627\\u062d\\u060c \\u064a\\u0631\\u062c\\u0649 \\u062a\\u0642\\u064a\\u064a\\u0645 \\u0627\\u0644\\u0645\\u062d\\u0627\\u0645\\u064a.","type":"case_closed"}',
                'read_at' => NULL,
                'created_at' => '2026-08-23 00:29:35',
                'updated_at' => '2026-08-23 00:29:35',
            ),
            31 => 
            array (
                'id' => 'e1895492-d183-4306-8920-849a170b1d2f',
                'type' => 'App\\Notifications\\Send_Proposal_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"proposal_id":"0aa81b67-f2d2-45d5-b5b4-6c8da9145fae","message":"You have received a new proposal. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:52:30',
                'updated_at' => '2026-08-22 09:52:30',
            ),
            32 => 
            array (
                'id' => 'ec4b0c22-59f1-49b8-9c94-27e2680b2ac6',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'bd69116a-1e9f-47fb-8cfe-ac93549c5202',
                'data' => '{"request_id":"1ed5a772-19d0-4fe3-b058-8808c5766557","title":"\\u062f\\u0639\\u0648\\u0649 \\u062a\\u0641\\u0631\\u064a\\u0642","message":"New request: \\u062f\\u0639\\u0648\\u0649 \\u062a\\u0641\\u0631\\u064a\\u0642"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:44:04',
                'updated_at' => '2026-08-22 09:44:04',
            ),
            33 => 
            array (
                'id' => 'ed3a4645-2db7-4e15-adb1-2c84cc4b4cc7',
                'type' => 'App\\Notifications\\Add_Session_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"session_id":"eb76d49d-e8a8-43f6-a174-495b29d3c8f9","message":"You have A new Seesion"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:57:36',
                'updated_at' => '2026-08-22 20:57:36',
            ),
            34 => 
            array (
                'id' => 'f23cebca-5891-4c0b-8305-18cfe4844ec4',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"request_id":"46c42f57-ec9d-4baa-b0fe-62831423eb7b","title":"\\u062f\\u0639\\u0648\\u0649 \\u062a\\u062b\\u0628\\u064a\\u062a \\u0628\\u064a\\u0639","message":"New request: \\u062f\\u0639\\u0648\\u0649 \\u062a\\u062b\\u0628\\u064a\\u062a \\u0628\\u064a\\u0639"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:45:14',
                'updated_at' => '2026-08-22 09:45:14',
            ),
            35 => 
            array (
                'id' => 'f271a6de-0a35-48f8-8ad3-7e0a17cce76f',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'bd69116a-1e9f-47fb-8cfe-ac93549c5202',
                'data' => '{"request_id":"377735d0-aa00-48ad-84ee-a9d4b292918d","title":"\\u062f\\u0639\\u0648\\u0649 \\u062a\\u062b\\u0628\\u064a\\u062a \\u0628\\u064a\\u0639","message":"New request: \\u062f\\u0639\\u0648\\u0649 \\u062a\\u062b\\u0628\\u064a\\u062a \\u0628\\u064a\\u0639"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:44:36',
                'updated_at' => '2026-08-22 09:44:36',
            ),
            36 => 
            array (
                'id' => 'fb26060e-27e8-4251-9fd2-fc16ca05e672',
                'type' => 'App\\Notifications\\Send_Request_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'data' => '{"request_id":"f9c5f085-61ac-4736-8ca7-f11874c7cb16","title":"\\u062f\\u0639\\u0648\\u0649 \\u062a\\u0641\\u0631\\u064a\\u0642","message":"New request: \\u062f\\u0639\\u0648\\u0649 \\u062a\\u0641\\u0631\\u064a\\u0642"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 09:43:07',
                'updated_at' => '2026-08-22 09:43:07',
            ),
            37 => 
            array (
                'id' => 'fd41e63d-51fd-4558-9958-74e607d2ad0e',
                'type' => 'App\\Notifications\\Send_Proposal_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"proposal_id":"b6a25f7d-7016-4834-830c-3b1e6ff2ea3c","message":"You have received a new proposal. Please check it out."}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 10:13:49',
                'updated_at' => '2026-08-22 10:13:49',
            ),
            38 => 
            array (
                'id' => 'fdd46c36-f571-4c65-892a-85a4d6994b84',
                'type' => 'App\\Notifications\\Add_Session_Notification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'data' => '{"session_id":"d939e12f-04c4-467d-a5b7-98df6829341d","message":"You have A new Seesion"}',
                'read_at' => NULL,
                'created_at' => '2026-08-22 20:56:33',
                'updated_at' => '2026-08-22 20:56:33',
            ),
        ));
        
        
    }
}