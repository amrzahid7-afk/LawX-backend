<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DocumentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('documents')->delete();
        
        \DB::table('documents')->insert(array (
            0 => 
            array (
                'id' => '08b4ad9a-c8b0-4570-8db4-75ee091a64ac',
                'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787393038/lawx/documents/proposals/af6obusetlfdh2gvottu.pdf',
                'signed_file_path' => NULL,
                'type' => 'pdf',
                'file_size' => NULL,
                'case_id' => NULL,
                'invoice_id' => NULL,
                'legal_request_id' => NULL,
                'proposal_id' => '3e82deb1-e3db-43cc-aedd-207f4e6efd22',
                'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'extracted_text' => NULL,
                'ai_tags' => NULL,
                'ai_summary' => NULL,
                'has_summary' => 0,
                'has_versions' => 0,
                'version_number' => 1,
                'parent_id' => NULL,
                'created_at' => '2026-08-22 10:03:58',
                'updated_at' => '2026-08-22 10:03:58',
            ),
            1 => 
            array (
                'id' => '0b69e612-2b39-4d5e-bfc7-e74b44238ebb',
                'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787420694/lawx/documents/case_files/4f506dd8-8c11-46db-b6c1-0f8ebe97c295/m8kqoadmznry1waseroj.jpg',
                'signed_file_path' => NULL,
                'type' => 'مذكرة دفاع',
                'file_size' => 96315,
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'invoice_id' => NULL,
                'legal_request_id' => NULL,
                'proposal_id' => NULL,
                'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'extracted_text' => 'عقد بيع عقار
إنه في يوم $}اليوم{ الموافق $}التاريخ{، وبمحض إرادة الطرفين وهما بكامل األهلية المعتبرة شرعا وقانونا ،
تم االتفاق والتراضي بين كل من:
الطرف األول )البائع(: السيد $}اسم_البائع{، الجنسية $}جنسية_البائع{، ويحمل البطاقة الشخصية رقم
$}رقم_بطاقة_البائع{ الصادرة عن $}جهة_اإلصدار{، والمقيم في $}عنوان_البائع{.
الطرف الثاني )المشتري(: السيد $}اسم_المشتري{، الجنسية $}جنسية_المشتري{، ويحمل البطاقة الشخصية
رقم $}رقم_بطاقة_المشتري{، والمقيم في $}عنوان_المشتري{، هاتف رقم $}رقم_الهاتف{.
تمهيد:
لما كان الطرف األول يمتلك العقار الموضّح أدناه ملكية تامة خالية من أي حقوق للغير، وقد رغب في بيعه،
وقبل الطرف الثاني شراءه بعد أن عاينه المعاينة النافية للجهالة، فقد اتفق الطرفان على ما يلي:
البند األول: باع الطرف األول إلى الطرف الثاني العقار التالي: $}وصف_العقار{، الكائن في $}موقع_العقار{،
والبالغة مساحته $}مساحة_العقار{.
البند الثاني: تم هذا البيع بثمن إجمالي مقداره $}المبلغ_اإلجمالي{ )$}المبلغ_كتابة{(، دفع منه الطرف الثاني
عند توقيع هذا العقد مبلغ $}الدفعة_المقدمة{، ويُسدَّد الباقي وفق اآللية التالية: $}طريقة_السداد{.
البند الثالث: يلتزم الطرف األول بتسليم العقار خاليا من الشواغل في موعد أقصاه $}تاريخ_التسليم{.
البند الرابع: يتح ّمل الطرف $}جهة_تحمل_الرسوم{ كافة رسوم التسجيل ونقل الملكية.
البند الخامس: ر
ُح ّر هذا العقد من نسختين بيد كل طرف نسخة للعمل بموجبها عند الحاجة.
توقيع الطرف األول )البائع(: $}توقيع_البائع{
توقيع الطرف الثاني )المشتري(: $}توقيع_المشتري{',
                    'ai_tags' => '["عقد بيع", "عقار", "البائع", "المشتري", "ثمن إجمالي", "تسليم العقار", "رسوم التسجيل", "نقل الملكية"]',
                'ai_summary' => 'عقد بيع عقار يوضح اتفاقاً قانونياً بين طرفين (بائع ومشتري) على نقل ملكية عقار محدد المواصفات والمساحة مقابل مبلغ مالي متفق عليه، مع تحديد شروط تسليم العقار، آلية دفع الثمن، والطرف المسؤول عن تحمل رسوم التسجيل ونقل الملكية.',
                    'has_summary' => 0,
                    'has_versions' => 1,
                    'version_number' => 2,
                    'parent_id' => '74e04bc9-dee7-4337-9eeb-d613c136a9e3',
                    'created_at' => '2026-08-22 17:44:55',
                    'updated_at' => '2026-08-22 17:44:55',
                ),
                2 => 
                array (
                    'id' => '0cb14244-7cc2-4188-b058-f4bde39131ea',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787420628/lawx/documents/case_files/4f506dd8-8c11-46db-b6c1-0f8ebe97c295/alzu5rqnbinxxo9pawfj.jpg',
                    'signed_file_path' => NULL,
                    'type' => 'مذكرة دفاع',
                    'file_size' => 110643,
                    'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                    'invoice_id' => NULL,
                    'legal_request_id' => NULL,
                    'proposal_id' => NULL,
                    'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                    'extracted_text' => 'الجمهورية العربية السورية
وزارة العدل
محكمة دمشق
ديوان الفرائض

وثيقة حصر إرث شرعي

الرقم (٩٢٩)
التاريخ: ١٤٤٧/٨/٧ هـ الموافق: ٢٠٢٦/١/٢٦ م

القاضي الشرعي الأول المؤازر: رضوان محمود المدني
الكاتب: عبد الهادي عرموش

بناء على طلب (خليل حبشيه بن إبراهيم) شهادة المختار: برزه ٢٠٢٦/١/٢٦
وعلى حاشية المالية المؤرخة في: / / ٢٠م
بناءً على صورة قيد النفوس الصادرة عن أمين السجل المدني في/المركزية/
وعملاً بأحكام الفرائض الشرعية المقدرة في كتاب الله وسنة نبيه صلّى الله عليه وسلّم نقرر ثبوت وفاة:
المورّثة: فلك غره بنت محمد والدتها نفيسه تولد ١٩٤٩ الرقم الوطني (٠١٠١٠١٧٩٤١٦) بتاريخ: ١٢ / ١٢ / ٢٠٢٥، وانحصار إرثها الشرعي بورثتها المدرجة أسماؤهم أدناه:
أبناء (خليل ١٩٦٥ ومحمود ١٩٧٠) وبنات (سناء ١٩٦٦ ونفيسه ١٩٧١ ومياده ١٩٧٦ ونيروز ١٩٨٣) وهم أولاد المؤرث فلك غره من زوجها المتوفى قبلها إبراهيم حبشيه وفي حفيد (عبد الرحمن ٢٠٠١) وحفيدة (فلك ١٩٩١ وضحى ١٩٩٥) وهم أولاد ابن المؤرّثة المتوفى قبلها موفق حبشيه وفي أحفاد (اسامه ١٩٩٤ ومحمد ٢٠٠٦) وحفيدات (ايمان ١٩٩٠ ونور ١٩٩٨) وهم أولاد بنت المؤرّثة المتوفية قبلها ملك حبشيه. صحت المسألة الإرثية من أصل (١٠٥٦) سهم خصص لأبناء المؤرّثة كل واحد بمفرده (٢٠٤) سهم وخصص لبنات المؤرّثة كل واحدة بمفردها (١٠٢) سهم وخصص لكل واحد بمفرده من الأحفاد أولاد موفق حبشيه (٥٦) سهم وخصص لكل واحد بمفرده من الأحفاد أولاد ملك حبشيه (١٨) سهم.......... ولا وارث سوى ما ذكر والله أعلم.
-تضمين المستدعي رسم الوثيقة.

صدر يوم الاثنين ١٤٤٧/٨/٧ هـ الموافق: ٢٠٢٦/١/٢٦ م
الكاتب: عبد الهادي عرموش
القاضي: القاضي الشرعي الأول المؤازر في دمشق رضوان المدني
صورة طبق الأصل
٢ كانون الثاني ٢٠٢٦
رسم ٨٠٠ ل. س / لصالح صندوق التعاون
1 من 1',
                    'ai_tags' => '["حصر إرث شرعي", "محكمة دمشق", "ديوان الفرائض", "فلك غره بنت محمد", "توزيع الحصص الإرثية", "المسألة الإرثية", "الجمهورية العربية السورية"]',
                'ai_summary' => 'وثيقة حصر إرث شرعي صادرة عن محكمة دمشق (ديوان الفرائض) تحت الرقم (٩٢٩) وتاريخ ٢٠٢٦/١/٢٦م، تقرر ثبوت وفاة المورّثة (فلك غره بنت محمد) بتاريخ ٢٠٢٥/١٢/١٢م. تحدد الوثيقة الورثة الشرعيين وهم أبناؤها وبناتها وأحفادها من أولادها المتوفين قبلها (موفق وملكة)، وتوزع الحصص الإرثية للورثة من أصل مسألة قدرها (١٠٥٦) سهماً طبقاً للأحكام الشرعية.',
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 17:43:48',
                    'updated_at' => '2026-08-22 17:44:20',
                ),
                3 => 
                array (
                    'id' => '201c2235-f96e-4cc0-adac-6e8385d7a9ef',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/raw/upload/v1787361809/lawx/templates/originals/8486bad3-bfe7-4fb6-8a94-feb5f4a23379.docx',
                    'signed_file_path' => NULL,
                    'type' => 'template',
                    'file_size' => 15402,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => NULL,
                    'proposal_id' => NULL,
                    'uploaded_by' => 'd43c0bb0-2f26-4ad7-9144-2fa4f518d3ae',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 01:23:29',
                    'updated_at' => '2026-08-22 01:23:29',
                ),
                4 => 
                array (
                    'id' => '20506eab-8e01-418a-a4b4-354174754ced',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787393394/lawx/documents/proposals/pxhgvct15uswuuvutnr8.pdf',
                    'signed_file_path' => NULL,
                    'type' => 'pdf',
                    'file_size' => NULL,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => NULL,
                    'proposal_id' => '4975e8f9-25ff-476e-b15d-539342a5f662',
                    'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 10:09:55',
                    'updated_at' => '2026-08-22 10:09:55',
                ),
                5 => 
                array (
                    'id' => '27b6f870-8a7e-4e3f-9594-9829448c39fb',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787392019/lawx/documents/ntd3hbovfsmiucdlajbz.pdf',
                    'signed_file_path' => NULL,
                    'type' => 'pdf',
                    'file_size' => NULL,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => '06f5af97-8867-41b3-a524-63850bfe7bbc',
                    'proposal_id' => NULL,
                    'uploaded_by' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 09:46:59',
                    'updated_at' => '2026-08-22 09:46:59',
                ),
                6 => 
                array (
                    'id' => '2c6f5df7-2db7-4e8f-aa8e-a1feb582cb14',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787391258/lawx/documents/tyuhr37vkhqdzfs00jlo.pdf',
                    'signed_file_path' => NULL,
                    'type' => 'pdf',
                    'file_size' => NULL,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => '3ed93eec-e922-4215-a209-63956adcb193',
                    'proposal_id' => NULL,
                    'uploaded_by' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 09:34:18',
                    'updated_at' => '2026-08-22 09:34:18',
                ),
                7 => 
                array (
                    'id' => '485a17c2-2506-4842-b805-37ed9c0b6f99',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787391179/lawx/documents/i452fz6gghphokg5r2f3.pdf',
                    'signed_file_path' => NULL,
                    'type' => 'pdf',
                    'file_size' => NULL,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => '9ce6fabe-1d7a-4057-87b6-7ba18418b733',
                    'proposal_id' => NULL,
                    'uploaded_by' => '584d073b-a517-45ac-b79d-82f047e9ab3b',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 09:32:59',
                    'updated_at' => '2026-08-22 09:32:59',
                ),
                8 => 
                array (
                    'id' => '58852884-98c2-4fb8-8089-f5052157b3c2',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/raw/upload/v1787361946/lawx/templates/originals/f40c8dcd-765c-400a-bc54-d95090e8fd8f.docx',
                    'signed_file_path' => NULL,
                    'type' => 'template',
                    'file_size' => 14922,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => NULL,
                    'proposal_id' => NULL,
                    'uploaded_by' => 'd43c0bb0-2f26-4ad7-9144-2fa4f518d3ae',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 01:25:46',
                    'updated_at' => '2026-08-22 01:25:46',
                ),
                9 => 
                array (
                    'id' => '5d53a6e4-8410-44dc-a72d-5ac9d2ec4225',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787391886/lawx/documents/ljpibjkzv1xd2men8hhe.pdf',
                    'signed_file_path' => NULL,
                    'type' => 'pdf',
                    'file_size' => NULL,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => '377735d0-aa00-48ad-84ee-a9d4b292918d',
                    'proposal_id' => NULL,
                    'uploaded_by' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 09:44:46',
                    'updated_at' => '2026-08-22 09:44:46',
                ),
                10 => 
                array (
                    'id' => '5e8d292e-fd1d-4eef-985d-3ec9eb4528d3',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787391923/lawx/documents/scmhem3vl6kyejtlnjjy.pdf',
                    'signed_file_path' => NULL,
                    'type' => 'pdf',
                    'file_size' => NULL,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => '46c42f57-ec9d-4baa-b0fe-62831423eb7b',
                    'proposal_id' => NULL,
                    'uploaded_by' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 09:45:24',
                    'updated_at' => '2026-08-22 09:45:24',
                ),
                11 => 
                array (
                    'id' => '6904a9d3-465b-4fac-8a39-5225528b5276',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787420911/lawx/documents/case_files/4f506dd8-8c11-46db-b6c1-0f8ebe97c295/tomfvqasbpyeclz7ac9t.jpg',
                    'signed_file_path' => NULL,
                    'type' => 'براءة الذمة',
                    'file_size' => 135948,
                    'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                    'invoice_id' => NULL,
                    'legal_request_id' => NULL,
                    'proposal_id' => NULL,
                    'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                    'extracted_text' => 'الجمهورية العربية السورية
وزارة العدل
مطبوعات صندوق التعاون

السيد رئيس محكمة: البداية المدنية الثالثة الموقرة بدمشق
طلب إحالة إلى مديرية المالية مقدم من:
الجهة (المدعية - المدعى عليها):
غزل تومة وهشام صالح
يرجى الموافقة على إحالة طلبنا هذا إلى مديرية مالية دمشق
وذلك لاستيفاء ضريبة البيوع المتوجبة على بيع الحصة السهمية البالغ مقدارها ٥٠ حصة كاملة
من العقار أو المتجر المشاد على المحضر رقم ١٤٠١ من منطقة المزة العقارية
موضوع الدعوى المقيدة لديكم برقم أساس ٢٤٦٢ لعام ٢٠٢٢ والمحدد يوم ٨ / ٩ / ٢٠٢٢ موعداً للنظر فيها.
والقائمة بين المتداعيين غزل تومة وهشام صالح.

مع وافر الاحترام
حرر في: ٦ / ٨ / ٢٠٢٢
المستدعي

[ختم ديوان مديرية مالية محافظة دمشق، وارد بريد رسمي، رقم ٢١٠٢٦، تاريخ ١٢ آب ٢٠٢٢]

ربطاً صورة عن استدعاء الدعوى

قرار المحكمة:
إلى مديرية مالية دمشق
لاستيفاء الضريبة المالية المتوجبة على البيع موضوع الدعوى وإعلامنا بما يشعر بذلك قبل موعد الجلسة أصولاً
التاريخ: ١٦ / ٨ / ٢٠٢٢
رئيس المحكمة',
                    'ai_tags' => '["محكمة البداية المدنية", "مديرية مالية دمشق", "ضريبة البيوع", "حصة سهمية", "المزة العقارية", "غزل تومة", "هشام صالح", "قرار المحكمة"]',
                    'ai_summary' => 'طلب مقدم من غزل تومة وهشام صالح إلى رئيس محكمة البداية المدنية الثالثة بدمشق للموافقة على إحالة القضية إلى مديرية مالية دمشق، بهدف استيفاء ضريبة البيوع العقارية المترتبة على بيع 50 حصة سهمية كاملة من العقار رقم 1401 بمنطقة المزة العقارية، وذلك ضمن الدعوى رقم أساس 2462 لعام 2022. وقد قررت المحكمة مخاطبة المالية لاستيفاء الضريبة وإشعارها بذلك قبل الجلسة المحددة في 8 سبتمبر 2022.',
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 17:48:33',
                    'updated_at' => '2026-08-22 17:49:02',
                ),
                12 => 
                array (
                    'id' => '6ab0c6e4-d2d9-4160-a260-ed2a334009fe',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787393014/lawx/documents/proposals/jvhkf8al7dg5rx8cul7p.pdf',
                    'signed_file_path' => NULL,
                    'type' => 'pdf',
                    'file_size' => NULL,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => NULL,
                    'proposal_id' => '7c5d31e5-8d75-4c53-b981-fba60bc1927c',
                    'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 10:03:34',
                    'updated_at' => '2026-08-22 10:03:34',
                ),
                13 => 
                array (
                    'id' => '6bda98e0-2d46-4400-8169-d53ebf518835',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787391798/lawx/documents/jaap91w1hags2r64hcee.pdf',
                    'signed_file_path' => NULL,
                    'type' => 'pdf',
                    'file_size' => NULL,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => 'f9c5f085-61ac-4736-8ca7-f11874c7cb16',
                    'proposal_id' => NULL,
                    'uploaded_by' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 09:43:18',
                    'updated_at' => '2026-08-22 09:43:18',
                ),
                14 => 
                array (
                    'id' => '6e1de5a9-d335-4d2e-b74f-d9e31b199ea9',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787391959/lawx/documents/cbzfhnyfmfimlvszd5le.pdf',
                    'signed_file_path' => NULL,
                    'type' => 'pdf',
                    'file_size' => NULL,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => '82209c88-62b8-484f-b0fc-5b2ed52b76fb',
                    'proposal_id' => NULL,
                    'uploaded_by' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 09:46:00',
                    'updated_at' => '2026-08-22 09:46:00',
                ),
                15 => 
                array (
                    'id' => '6f5bbf37-72fd-4fc7-9ec0-30051cfefe87',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/raw/upload/v1787361835/lawx/templates/originals/d97286e5-44a3-4205-8e5b-7666b855d149.docx',
                    'signed_file_path' => NULL,
                    'type' => 'template',
                    'file_size' => 15240,
                    'case_id' => NULL,
                    'invoice_id' => NULL,
                    'legal_request_id' => NULL,
                    'proposal_id' => NULL,
                    'uploaded_by' => 'd43c0bb0-2f26-4ad7-9144-2fa4f518d3ae',
                    'extracted_text' => NULL,
                    'ai_tags' => NULL,
                    'ai_summary' => NULL,
                    'has_summary' => 0,
                    'has_versions' => 0,
                    'version_number' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2026-08-22 01:23:55',
                    'updated_at' => '2026-08-22 01:23:55',
                ),
                16 => 
                array (
                    'id' => '74e04bc9-dee7-4337-9eeb-d613c136a9e3',
                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787418781/lawx/documents/case_files/4f506dd8-8c11-46db-b6c1-0f8ebe97c295/h5262ge5dkpoubifs4kj.pdf',
                    'signed_file_path' => NULL,
                    'type' => 'ملف القسمة',
                    'file_size' => 63178,
                    'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                    'invoice_id' => NULL,
                    'legal_request_id' => NULL,
                    'proposal_id' => NULL,
                    'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                    'extracted_text' => 'عقد بيع عقار
إنه في يوم $}اليوم{ الموافق $}التاريخ{، وبمحض إرادة الطرفين وهما بكامل األهلية المعتبرة شرعا وقانونا ،
تم االتفاق والتراضي بين كل من:
الطرف األول )البائع(: السيد $}اسم_البائع{، الجنسية $}جنسية_البائع{، ويحمل البطاقة الشخصية رقم
$}رقم_بطاقة_البائع{ الصادرة عن $}جهة_اإلصدار{، والمقيم في $}عنوان_البائع{.
الطرف الثاني )المشتري(: السيد $}اسم_المشتري{، الجنسية $}جنسية_المشتري{، ويحمل البطاقة الشخصية
رقم $}رقم_بطاقة_المشتري{، والمقيم في $}عنوان_المشتري{، هاتف رقم $}رقم_الهاتف{.
تمهيد:
لما كان الطرف األول يمتلك العقار الموضّح أدناه ملكية تامة خالية من أي حقوق للغير، وقد رغب في بيعه،
وقبل الطرف الثاني شراءه بعد أن عاينه المعاينة النافية للجهالة، فقد اتفق الطرفان على ما يلي:
البند األول: باع الطرف األول إلى الطرف الثاني العقار التالي: $}وصف_العقار{، الكائن في $}موقع_العقار{،
والبالغة مساحته $}مساحة_العقار{.
البند الثاني: تم هذا البيع بثمن إجمالي مقداره $}المبلغ_اإلجمالي{ )$}المبلغ_كتابة{(، دفع منه الطرف الثاني
عند توقيع هذا العقد مبلغ $}الدفعة_المقدمة{، ويُسدَّد الباقي وفق اآللية التالية: $}طريقة_السداد{.
البند الثالث: يلتزم الطرف األول بتسليم العقار خاليا من الشواغل في موعد أقصاه $}تاريخ_التسليم{.
البند الرابع: يتح ّمل الطرف $}جهة_تحمل_الرسوم{ كافة رسوم التسجيل ونقل الملكية.
البند الخامس: ر
ُح ّر هذا العقد من نسختين بيد كل طرف نسخة للعمل بموجبها عند الحاجة.
توقيع الطرف األول )البائع(: $}توقيع_البائع{
توقيع الطرف الثاني )المشتري(: $}توقيع_المشتري{',
                        'ai_tags' => '["عقد بيع", "عقار", "البائع", "المشتري", "ثمن إجمالي", "تسليم العقار", "رسوم التسجيل", "نقل الملكية"]',
                    'ai_summary' => 'عقد بيع عقار يوضح اتفاقاً قانونياً بين طرفين (بائع ومشتري) على نقل ملكية عقار محدد المواصفات والمساحة مقابل مبلغ مالي متفق عليه، مع تحديد شروط تسليم العقار، آلية دفع الثمن، والطرف المسؤول عن تحمل رسوم التسجيل ونقل الملكية.',
                        'has_summary' => 0,
                        'has_versions' => 0,
                        'version_number' => 1,
                        'parent_id' => NULL,
                        'created_at' => '2026-08-22 17:13:03',
                        'updated_at' => '2026-08-22 17:13:23',
                    ),
                    17 => 
                    array (
                        'id' => '7c429b19-81c3-4b2e-be79-21fd524d1600',
                        'file_path' => 'https://res.cloudinary.com/mqrzbunw/raw/upload/v1787361920/lawx/templates/originals/1dc96686-f5be-4f14-b7d4-2062dee7d48a.docx',
                        'signed_file_path' => NULL,
                        'type' => 'template',
                        'file_size' => 15123,
                        'case_id' => NULL,
                        'invoice_id' => NULL,
                        'legal_request_id' => NULL,
                        'proposal_id' => NULL,
                        'uploaded_by' => 'd43c0bb0-2f26-4ad7-9144-2fa4f518d3ae',
                        'extracted_text' => NULL,
                        'ai_tags' => NULL,
                        'ai_summary' => NULL,
                        'has_summary' => 0,
                        'has_versions' => 0,
                        'version_number' => 1,
                        'parent_id' => NULL,
                        'created_at' => '2026-08-22 01:25:20',
                        'updated_at' => '2026-08-22 01:25:20',
                    ),
                    18 => 
                    array (
                        'id' => '7f629ef2-87df-4ede-b519-5262d8ec15ad',
                        'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787433646/lawx/templates/generated/kh9walpmwb9qwj5olboi.pdf',
                        'signed_file_path' => NULL,
                        'type' => 'pdf',
                        'file_size' => NULL,
                        'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                        'invoice_id' => NULL,
                        'legal_request_id' => NULL,
                        'proposal_id' => NULL,
                        'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                        'extracted_text' => NULL,
                        'ai_tags' => NULL,
                        'ai_summary' => NULL,
                        'has_summary' => 0,
                        'has_versions' => 0,
                        'version_number' => 1,
                        'parent_id' => NULL,
                        'created_at' => '2026-08-22 21:20:47',
                        'updated_at' => '2026-08-22 21:20:47',
                    ),
                    19 => 
                    array (
                        'id' => '8b70154c-66f5-44bd-8448-0b172b629667',
                        'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787420832/lawx/documents/case_files/4f506dd8-8c11-46db-b6c1-0f8ebe97c295/ul8qccod30eo9a1wogkl.jpg',
                        'signed_file_path' => NULL,
                        'type' => 'قرار حكم',
                        'file_size' => 51210,
                        'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                        'invoice_id' => NULL,
                        'legal_request_id' => NULL,
                        'proposal_id' => NULL,
                        'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                        'extracted_text' => 'المحامية
مروة جمال الرزيق
موبايل: ٠٩٣٢٤٤١٥٩٣

Lawyer
Marwa WdJamal AlRuzbaik
Mob: 0932441593

مقام محكمة البداية المدنية الموقرة في دمشق

الجهة المدعية : يوسف المصري بن عبدو تمثله المحامية مروة الرزيق
الجهة المدعى عليها : حسيب حاج موسى بن محمد حسن يمثله المحامي خلدون فيومي الشهير بالخطيب

الدعوى : تثبيت بيع

- لما كانت الجهة المدعى عليها تملك حصة سهمية بالغة مائتان من العقار /١٥٦٧/ من منطقة برزة العقارية بموجب وكالة الكاتب بالعدل في دمشق ذات رقم خاص ٥٨ عام ٢٠٢٢ سجل ٦٨٠ لعام ٢٠٢٢.
- لما كانت الجهة المدعية قد اشترت من الجهة المدعى عليها حصة سهمية قدرها ٣٢ سهم من العقار /١٥٦٧/ من المنطقة العقارية برزة موضوع الدعوى والتي هي عبارة عن أرض زراعية مساحة ١٤م٢
- وحيث أن الجهة المدعية ممتنعة عن تثبيت البيع

لذلك جئنا ملتمسين من مقامكم الكريم بعد قيد الدعوى و وضع إشارة الدعوى على صحيفة العقار /١٥٦٧/ منطقة برزة العقارية و الحكم وفق ما يلي :

١- دعوة الأطراف إلى أقرب جلسة ممكنة.
٢- تثبيت البيع الجاري بين الجهة المدعية والجهة المدعى عليه للحصة السهمية ٣٢ سهم من العقار /١٥٦٧/ منطقة برزة العقارية وتسجيل المبيع على اسم الجهة المدعية لدى أمين السجل العقاري أصولاً.
٣- ترقين إشارة الدعوى من على صحيفة العقار تلازماً مع التسجيل.
٤- تضمين الجهة المدعى عليها الرسوم و المصاريف.

بكل تحفظ واحترام

دمشق في ١٩/ ٤/ ٢٠٢٦ م

المحامي الوكيل',
                        'ai_tags' => '["محكمة البداية المدنية", "تثبيت بيع", "حصة سهمية", "برزة العقارية", "السجل العقاري", "إشارة الدعوى", "يوسف المصري", "حسيب حاج موسى"]',
                    'ai_summary' => 'استدعاء دعوى تثبيت بيع مقدمة إلى محكمة البداية المدنية في دمشق من الجهة المدعية (يوسف المصري) ضد الجهة المدعى عليها (حسيب حاج موسى)، يطالب فيها المدعي بتثبيت شراء حصة سهمية قدرها ٣٢ سهماً من العقار رقم ١٥٦٧ في منطقة برزة العقارية، مع التماس وضع إشارة الدعوى على صحيفة العقار ونقل الملكية لاسم المدعي في السجل العقاري.',
                        'has_summary' => 0,
                        'has_versions' => 0,
                        'version_number' => 1,
                        'parent_id' => NULL,
                        'created_at' => '2026-08-22 17:47:13',
                        'updated_at' => '2026-08-22 17:47:37',
                    ),
                    20 => 
                    array (
                        'id' => '937617e8-175d-4a6a-b696-c73a24e281a8',
                        'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787418829/lawx/documents/case_files/4f506dd8-8c11-46db-b6c1-0f8ebe97c295/qbrngr2pu0wxptzvorxh.pdf',
                        'signed_file_path' => NULL,
                        'type' => 'ملف القسمة',
                        'file_size' => 63178,
                        'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                        'invoice_id' => NULL,
                        'legal_request_id' => NULL,
                        'proposal_id' => NULL,
                        'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                        'extracted_text' => 'عقد بيع عقار

إنه في يوم $}اليوم{ الموافق $}التاريخ{، وبمحض إرادة الطرفين وهما بكامل األهلية المعتبرة شرعاً وقانوناً، تم االتفاق والتراضي بين كل من:

الطرف األول (البائع): السيد $}اسم_البائع{، الجنسية $}جنسية_البائع{، ويحمل البطاقة الشخصية رقم $}رقم_بطاقة_البائع{ الصادرة عن $}جهة_اإلصدار{، والمقيم في $}عنوان_البائع{.

الطرف الثاني (المشتري): السيد $}اسم_المشتري{، الجنسية $}جنسية_المشتري{، ويحمل البطاقة الشخصية رقم $}رقم_بطاقة_المشتري{، والمقيم في $}عنوان_المشتري{، هاتف رقم $}رقم_الهاتف{.

تمهيد:
لما كان الطرف األول يمتلك العقار الموضّح أدناه ملكية تامة خالية من أي حقوق للغير، وقد رغب في بيعه، وقبل الطرف الثاني شراءه بعد أن عاينه المعاينة النافية للجهالة، فقد اتفق الطرفان على ما يلي:

البند األول: باع الطرف األول إلى الطرف الثاني العقار التالي: $}وصف_العقار{، الكائن في $}موقع_العقار{، والبالغة مساحته $}مساحة_العقار{.

البند الثاني: تم هذا البيع بثمن إجمالي مقداره $}المبلغ_اإلجمالي{ ($}المبلغ_كتابة{)، دفع منه الطرف الثاني عند توقيع هذا العقد مبلغ $}الدفعة_المقدمة{، ويُسدَّد الباقي وفق اآللية التالية: $}طريقة_السداد{.

البند الثالث: يلتزم الطرف األول بتسليم العقار خالياً من الشواغل في موعد أقصاه $}تاريخ_التسليم{.

البند الرابع: يتحمّل الطرف $}جهة_تحمل_الرسوم{ كافة رسوم التسجيل ونقل الملكية.

البند الخامس: حُرّر هذا العقد من نسختين بيد كل طرف نسخة للعمل بموجبها عند الحاجة.

توقيع الطرف األول (البائع): $}توقيع_البائع{
توقيع الطرف الثاني (المشتري): $}توقيع_المشتري{',
                            'ai_tags' => '["عقد بيع", "عقار", "البائع", "المشتري", "تسليم العقار", "رسوم التسجيل", "نقل الملكية"]',
                        'ai_summary' => 'نموذج عقد بيع عقار يوثق اتفاقاً بين طرفين (بائع ومشتري) كاملي الأهلية، يحدد مواصفات العقار وموقعه ومساحته، مع تفصيل القيمة الإجمالية وطريقة السداد وموعد التسليم، بالإضافة إلى تحديد الطرف المسؤول عن رسوم التسجيل ونقل الملكية.',
                            'has_summary' => 0,
                            'has_versions' => 0,
                            'version_number' => 1,
                            'parent_id' => NULL,
                            'created_at' => '2026-08-22 17:13:50',
                            'updated_at' => '2026-08-22 17:14:02',
                        ),
                        21 => 
                        array (
                            'id' => 'a7e7c1d1-9618-44ca-86cc-3d39f7bf579d',
                            'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787392993/lawx/documents/proposals/ij6l3yql2z0vwbdgk11r.pdf',
                            'signed_file_path' => NULL,
                            'type' => 'pdf',
                            'file_size' => NULL,
                            'case_id' => NULL,
                            'invoice_id' => NULL,
                            'legal_request_id' => NULL,
                            'proposal_id' => 'cf402f8b-c66b-4193-a598-4fd2cca64936',
                            'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                            'extracted_text' => NULL,
                            'ai_tags' => NULL,
                            'ai_summary' => NULL,
                            'has_summary' => 0,
                            'has_versions' => 0,
                            'version_number' => 1,
                            'parent_id' => NULL,
                            'created_at' => '2026-08-22 10:03:14',
                            'updated_at' => '2026-08-22 10:03:14',
                        ),
                        22 => 
                        array (
                            'id' => 'b0771bb7-5e3d-4595-9f18-eca80dd65019',
                            'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787392349/lawx/documents/proposals/j3qneocj7ffnqsi6n04b.pdf',
                            'signed_file_path' => NULL,
                            'type' => 'pdf',
                            'file_size' => NULL,
                            'case_id' => NULL,
                            'invoice_id' => NULL,
                            'legal_request_id' => NULL,
                            'proposal_id' => '0aa81b67-f2d2-45d5-b5b4-6c8da9145fae',
                            'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                            'extracted_text' => NULL,
                            'ai_tags' => NULL,
                            'ai_summary' => NULL,
                            'has_summary' => 0,
                            'has_versions' => 0,
                            'version_number' => 1,
                            'parent_id' => NULL,
                            'created_at' => '2026-08-22 09:52:30',
                            'updated_at' => '2026-08-22 09:52:30',
                        ),
                        23 => 
                        array (
                            'id' => 'bdce0837-5263-4b0d-b4a4-29eec7694e18',
                            'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787393055/lawx/documents/proposals/guxibut2nbjh5ybvllmm.pdf',
                            'signed_file_path' => NULL,
                            'type' => 'pdf',
                            'file_size' => NULL,
                            'case_id' => NULL,
                            'invoice_id' => NULL,
                            'legal_request_id' => NULL,
                            'proposal_id' => '1f364714-36e0-4729-bee7-742d9eaf6983',
                            'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                            'extracted_text' => NULL,
                            'ai_tags' => NULL,
                            'ai_summary' => NULL,
                            'has_summary' => 0,
                            'has_versions' => 0,
                            'version_number' => 1,
                            'parent_id' => NULL,
                            'created_at' => '2026-08-22 10:04:16',
                            'updated_at' => '2026-08-22 10:04:16',
                        ),
                        24 => 
                        array (
                            'id' => 'be2e59aa-7441-4566-8780-ac1a2b3837c3',
                            'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787393691/lawx/documents/proposals/bfxl5ufgbjeou4pq49kj.pdf',
                            'signed_file_path' => NULL,
                            'type' => 'pdf',
                            'file_size' => NULL,
                            'case_id' => NULL,
                            'invoice_id' => NULL,
                            'legal_request_id' => NULL,
                            'proposal_id' => '06144483-ffef-41d1-b9ee-f79865badb38',
                            'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                            'extracted_text' => NULL,
                            'ai_tags' => NULL,
                            'ai_summary' => NULL,
                            'has_summary' => 0,
                            'has_versions' => 0,
                            'version_number' => 1,
                            'parent_id' => NULL,
                            'created_at' => '2026-08-22 10:14:52',
                            'updated_at' => '2026-08-22 10:14:52',
                        ),
                        25 => 
                        array (
                            'id' => 'c574e107-6b5f-4ad4-9e02-42358dc2d4c6',
                            'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787393628/lawx/documents/proposals/luxmstc41h5fxhp6srth.pdf',
                            'signed_file_path' => NULL,
                            'type' => 'pdf',
                            'file_size' => NULL,
                            'case_id' => NULL,
                            'invoice_id' => NULL,
                            'legal_request_id' => NULL,
                            'proposal_id' => 'b6a25f7d-7016-4834-830c-3b1e6ff2ea3c',
                            'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                            'extracted_text' => NULL,
                            'ai_tags' => NULL,
                            'ai_summary' => NULL,
                            'has_summary' => 0,
                            'has_versions' => 0,
                            'version_number' => 1,
                            'parent_id' => NULL,
                            'created_at' => '2026-08-22 10:13:49',
                            'updated_at' => '2026-08-22 10:13:49',
                        ),
                        26 => 
                        array (
                            'id' => 'c7feb8fc-0479-4e24-9513-82b8a8008f80',
                            'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787391987/lawx/documents/trhyyd8vzi0k2lef9cax.pdf',
                            'signed_file_path' => NULL,
                            'type' => 'pdf',
                            'file_size' => NULL,
                            'case_id' => NULL,
                            'invoice_id' => NULL,
                            'legal_request_id' => 'ab840341-50d0-4161-b0bd-b11de6b3f58a',
                            'proposal_id' => NULL,
                            'uploaded_by' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                            'extracted_text' => NULL,
                            'ai_tags' => NULL,
                            'ai_summary' => NULL,
                            'has_summary' => 0,
                            'has_versions' => 0,
                            'version_number' => 1,
                            'parent_id' => NULL,
                            'created_at' => '2026-08-22 09:46:28',
                            'updated_at' => '2026-08-22 09:46:28',
                        ),
                        27 => 
                        array (
                            'id' => 'cce27a88-e5b8-4a69-aebb-9d72812a647e',
                            'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787391223/lawx/documents/ab38gp6erwlihjbxdaml.pdf',
                            'signed_file_path' => NULL,
                            'type' => 'pdf',
                            'file_size' => NULL,
                            'case_id' => NULL,
                            'invoice_id' => NULL,
                            'legal_request_id' => 'd1aa47d4-549c-45a9-9b50-36b310099b43',
                            'proposal_id' => NULL,
                            'uploaded_by' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                            'extracted_text' => NULL,
                            'ai_tags' => NULL,
                            'ai_summary' => NULL,
                            'has_summary' => 0,
                            'has_versions' => 0,
                            'version_number' => 1,
                            'parent_id' => NULL,
                            'created_at' => '2026-08-22 09:33:44',
                            'updated_at' => '2026-08-22 09:33:44',
                        ),
                        28 => 
                        array (
                            'id' => 'd256a0b7-51f3-419b-ae0e-06e552b9769d',
                            'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787420727/lawx/documents/case_files/4f506dd8-8c11-46db-b6c1-0f8ebe97c295/mdz1d3ylhkr069eknjhs.jpg',
                            'signed_file_path' => NULL,
                            'type' => 'مرافعة 1',
                            'file_size' => 65962,
                            'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                            'invoice_id' => NULL,
                            'legal_request_id' => NULL,
                            'proposal_id' => NULL,
                            'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                            'extracted_text' => 'مكتب الشعال للمحاماة

للتقاضي مما يعتبر سببا لاعلان انعدام الحكم الصادر نتيجة لهذا ، وهذا ما استقر عليه الاجتهاد القضائي :
{إن أركان الحكم القضائية الأساسية هي أن يكون صادرا عن جهة قضائية مختصة تتمتع بولاية القضاء وعن محكمة مشكلة تشكيلا صحيحا في خصومة قائمة ما بين الطرفين تتوافر فيهما أهلية التقاضي ويتعين أن يكون الحكم مكتوبا وان يتضمن بياناته الأساسية يعتبر الحكم القضائي معدوما إذا ما صدر دون تبليغ الخصم بالدعوى .
إن صحة التمثيل من النظام العام ويحق للمحكمة إثارته من تلقاء نفسها حتى أمام محكمه النقض } .
(قرار محكمة النقض رقم ١٤٤٩ أساس ١٠٢٠ لعام ١٩٩٥ تاريخ ١٧/١٢/١٩٩٥ المنشور في مجلة القانون لعام ١٩٩٦ صفحة ٢٥٥) .

ولما كان الفقه والاجتهاد القضائي مستقرين على أن دعوى الانعدام تقدم إلى المحكمة المصدرة للقرار موضوعها .
{- الأحكام المعدومة تعتبر غير موجودة قانونا ولا تستلزم الطعن فيها .
- طلب انعدام القرار يرفع إلى نفس المحكمة التي أصدرته }.
(قرار محكمة النقض رقم ٩٢ أساس ٣٦٨ تاريخ ١٠/٤/١٩٨٦ المنشور في مجلة المحامون لعام ١٩٨٧ صفحة ٨٩٨) .',
                                'ai_tags' => '["مكتب الشعال للمحاماة", "انعدام الحكم", "الاجتهاد القضائي", "محكمة النقض", "أركان الحكم القضائي", "أهلية التقاضي", "صحة التمثيل", "النظام العام", "دعوى الانعدام", "التبليغ"]',
                            'ai_summary' => 'توضيح قانوني صادر عن مكتب الشعال للمحاماة يستعرض أركان الحكم القضائي الأساسية وشروط صحته، مبيناً حالات انعدام الأحكام القضائية (مثل عدم تبليغ الخصم أو بطلان التمثيل القانوني). ويؤكد النص مستنداً إلى اجتهادات محكمة النقض أن الأحكام المعدومة تعتبر غير موجودة قانوناً ولا تتطلب طعناً عادياً، بل ترفع دعوى انعدام القرار مباشرة أمام المحكمة التي أصدرت الحكم نفسه.',
                                'has_summary' => 0,
                                'has_versions' => 0,
                                'version_number' => 1,
                                'parent_id' => NULL,
                                'created_at' => '2026-08-22 17:45:27',
                                'updated_at' => '2026-08-22 17:45:54',
                            ),
                            29 => 
                            array (
                                'id' => 'd898cd13-10ba-4e49-ac60-e7afd33f6b14',
                                'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787420931/lawx/documents/case_files/4f506dd8-8c11-46db-b6c1-0f8ebe97c295/qraw0sg8qx6r9qcbtobt.jpg',
                                'signed_file_path' => NULL,
                                'type' => 'مصدقة الخارجية',
                                'file_size' => 135948,
                                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                                'invoice_id' => NULL,
                                'legal_request_id' => NULL,
                                'proposal_id' => NULL,
                                'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                                'extracted_text' => 'الجمهورية العربية السورية
وزارة العدل
مطبوعات صندوق التعاون

السيد رئيس محكمة: البداية المدنية الثالثة الموقرة بدمشق
طلب إحالة إلى مديرية المالية مقدم من:
الجهة ( المدعية - المدعى عليها ):
غفران توتة ضد هشام صالح

يرجى الموافقة على إحالة طلبنا هذا إلى مديرية مالية دمشق
وذلك لاستيفاء ضريبة البيوع المتوجبة على بيع الحصة السهمية البالغ مقدارها ٥٠٠ حصة سهم
من العقار أو المتجر المشاد على المحضر رقم ١٤٠١ من منطقة المزة العقارية
موضوع الدعوى المقيدة لديكم برقم أساس ٢٤٦٢ لعام ٢٠٢٢ والمحدد يوم ٨ / ٩ / ٢٠٢٢ موعداً للنظر فيها.
والقائمة بين المتداعيين غفران توتة وهشام صالح

مع وافر الاحترام
حرر في: ٦ / ٨ / ٢٠٢٢
المستدعي

[حاشية وختم]: ديوان مديرية مالية محافظة دمشق - وارد بريد رسمي وزارات ومديريات الدولة رقم ٢١٠٢٦ تاريخ ١٢ آب ٢٠٢٢

قرار المحكمة:
إلى مديرية مالية دمشق
لاستيفاء الضريبة المالية المتوجبة على البيع موضوع الدعوى وإعلامنا بما يشعر بذلك قبل موعد الجلسة أصولاً
التاريخ: ٦ / ٨ / ٢٠٢٢
رئيس المحكمة',
                                'ai_tags' => '["الجمهورية العربية السورية", "وزارة العدل", "محكمة البداية المدنية الثالثة بدمشق", "مديرية مالية دمشق", "ضريبة البيوع", "حصة سهمية", "المزة العقارية", "قرار المحكمة", "غفران توتة", "هشام صالح"]',
                            'ai_summary' => 'طلب إحالة موجه إلى رئيس محكمة البداية المدنية الثالثة بدمشق من المدعية غفران توتة في الدعوى المقامة ضد هشام صالح (برقم أساس 2462 لعام 2022)، للموافقة على مخاطبة مديرية مالية دمشق لاستيفاء ضريبة البيوع العقارية المترتبة على بيع حصة سهمية مقدارها 500 حصة من العقار رقم 1401 بمنطقة المزة العقارية. وقد أصدر رئيس المحكمة قراره بالموافقة وإحالة الكتاب للمالية لاستيفاء الضريبة وإشعار المحكمة بذلك قبل موعد الجلسة المقرر في 8 أيلول 2022.',
                                'has_summary' => 0,
                                'has_versions' => 0,
                                'version_number' => 1,
                                'parent_id' => NULL,
                                'created_at' => '2026-08-22 17:48:53',
                                'updated_at' => '2026-08-22 17:49:24',
                            ),
                            30 => 
                            array (
                                'id' => 'da1cded5-2793-41fc-9336-6f45c01bc954',
                                'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787393709/lawx/documents/proposals/okbg2v7kmsjmuunuqiqe.pdf',
                                'signed_file_path' => NULL,
                                'type' => 'pdf',
                                'file_size' => NULL,
                                'case_id' => NULL,
                                'invoice_id' => NULL,
                                'legal_request_id' => NULL,
                                'proposal_id' => 'dbffe769-49a4-4da2-bfb6-9e37789917e5',
                                'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                                'extracted_text' => NULL,
                                'ai_tags' => NULL,
                                'ai_summary' => NULL,
                                'has_summary' => 0,
                                'has_versions' => 0,
                                'version_number' => 1,
                                'parent_id' => NULL,
                                'created_at' => '2026-08-22 10:15:10',
                                'updated_at' => '2026-08-22 10:15:10',
                            ),
                            31 => 
                            array (
                                'id' => 'e60294fb-30dc-4cd5-ba8f-7ff63974ec50',
                                'file_path' => 'https://res.cloudinary.com/mqrzbunw/raw/upload/v1787361977/lawx/templates/originals/1b76749b-7ca8-446b-b8ff-d8a74c18c4df.docx',
                                'signed_file_path' => NULL,
                                'type' => 'template',
                                'file_size' => 15063,
                                'case_id' => NULL,
                                'invoice_id' => NULL,
                                'legal_request_id' => NULL,
                                'proposal_id' => NULL,
                                'uploaded_by' => 'd43c0bb0-2f26-4ad7-9144-2fa4f518d3ae',
                                'extracted_text' => NULL,
                                'ai_tags' => NULL,
                                'ai_summary' => NULL,
                                'has_summary' => 0,
                                'has_versions' => 0,
                                'version_number' => 1,
                                'parent_id' => NULL,
                                'created_at' => '2026-08-22 01:26:17',
                                'updated_at' => '2026-08-22 01:26:17',
                            ),
                            32 => 
                            array (
                                'id' => 'e68c390f-b2ce-4f4a-bd84-4316636e3337',
                                'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787435641/lawx/documents/signatures/xzaofw3tvs4wpr1nenru.pdf',
                                'signed_file_path' => NULL,
                                'type' => 'sign_request_document',
                                'file_size' => NULL,
                                'case_id' => NULL,
                                'invoice_id' => NULL,
                                'legal_request_id' => NULL,
                                'proposal_id' => NULL,
                                'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                                'extracted_text' => 'عقد بيع عقار

إنه في يوم الاحد الموافق 1 / 1 / 2025، وبمحض إرادة الطرفين وهما بكامل الأهلية المعتبرة شرعاً وقانوناً، تم الاتفاق والتراضي بين كل من:

الطرف الأول (البائع): السيد أحمد الميداني، الجنسية سوري، ويحمل البطاقة الشخصية رقم 01010151226 الصادرة عن وزارة الداخلية، والمقيم في المزة، الشيخ سعد.

الطرف الثاني (المشتري): السيد علي خير الله، الجنسية سوري، ويحمل البطاقة الشخصية رقم 01010253416، والمقيم في جرمانا، هاتف رقم 5142059.

تمهيد:
لما كان الطرف الأول يمتلك العقار الموضّح أدناه ملكية تامة خالية من أي حقوق للغير، وقد رغب في بيعه، وقبل الطرف الثاني شراءه بعد أن عاينه المعاينة النافية للجهالة، فقد اتفق الطرفان على ما يلي:

البند الأول: باع الطرف الأول إلى الطرف الثاني العقار التالي: أرض أميري والغراس ملك، الكائن في حرستا، والبالغة مساحته 2000.

البند الثاني: تم هذا البيع بثمن إجمالي مقداره 90,000,000 (تسعون مليون ل.س)، دفع منه الطرف الثاني عند توقيع هذا العقد مبلغ 50,000,000، ويُسدّد الباقي وفق الآلية التالية: نقدا.

البند الثالث: يلتزم الطرف الأول بتسليم العقار خالياً من الشواغل في موعد أقصاه 1 / 1 / 2025.

البند الرابع: يتحمّل الطرف الشاري كافة رسوم التسجيل ونقل الملكية.

البند الخامس: حُرّر هذا العقد من نسختين بيد كل طرف نسخة للعمل بموجبها عند الحاجة.

توقيع الطرف الأول (البائع):
توقيع الطرف الثاني (المشتري):',
                                    'ai_tags' => '["عقد بيع عقار", "أحمد الميداني", "علي خير الله", "حرستا", "أرض أميري", "تسعون مليون", "رسوم التسجيل", "نقل الملكية"]',
                                    'ai_summary' => 'عقد بيع عقار محرر بتاريخ 1 يناير 2025 بين البائع أحمد الميداني والمشتري علي خير الله. يتضمن العقد بيع عقار عبارة عن أرض أميري والغراس ملك تقع في منطقة حرستا بمساحة 2000 متر مربع، لقاء مبلغ إجمالي قدره 90 مليون ليرة سورية، سُدّد منه 50 مليون ليرة عند التوقيع على أن يُدفع الباقي نقداً. كما يلتزم البائع بتسليم العقار خالياً من الشواغل في موعد أقصاه 1 يناير 2025، ويتحمل المشتري رسوم التسجيل ونقل الملكية.',
                                    'has_summary' => 0,
                                    'has_versions' => 0,
                                    'version_number' => 1,
                                    'parent_id' => NULL,
                                    'created_at' => '2026-08-22 21:54:03',
                                    'updated_at' => '2026-08-22 21:54:57',
                                ),
                                33 => 
                                array (
                                    'id' => 'ebc42708-284c-4ca9-8a80-bf5434759869',
                                    'file_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787420535/lawx/documents/case_files/4f506dd8-8c11-46db-b6c1-0f8ebe97c295/tb6dsi6dax3p3jyll3hj.jpg',
                                    'signed_file_path' => NULL,
                                    'type' => 'طعن حكم',
                                    'file_size' => 98399,
                                    'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                                    'invoice_id' => NULL,
                                    'legal_request_id' => NULL,
                                    'proposal_id' => NULL,
                                    'uploaded_by' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                                'extracted_text' => 'الأوراق المطلوبة في حال كان مقدم طلب التسجيل غير مالك المتجر: ١- تقديم طلب من قبل طالب تسجيل المتجر أو وكيله القانوني مرفق به إحالة من قبل المحكمة المختصة بناء على دعوى منظورة أمامها وهو طرفا فيها ويسجل الطلب لدى أمانة السجل (شعبة المتجر) في سجل المتجر بعد تسجيلها في ديوان المديرية المختصة ٢- قيد عقاري للعقار القائم عليه المتجر. ٣- بيان صادر من مديرية المالية المختصة بناء على إحالة من أمين السجل التجاري المختص لبيان التكليف الضريبي على المتجر المذكور. ٤- إخراج قيد مالي للعقار القائم عليه المتجر. ٥- مخطط افرازي أو معماري إضافة إلى بيان مساحة للعقار القائم عليه المتجر. ٦- بالنسبة للعقارات القائمة عليها المتاجر و لا يتوفر لها صحيفة عقارية و مخططات إفرازية و بيانات المساحة يتم الاعتماد على الكشف و القيد المالي فقط دون الحاجة لإبراز قيد عقاري. ٧- إجراء الكشف على المتجر من قبل موظفين من دائرة الشركات المختصة (أمين السجل التجاري مع أحد العاملين في السجل التجاري) مع مهندس من جدول الخبراء المعتمد من وزارة العدل أو المهندسين العاملين في بلديات أو دوائر الخدمات وذلك على نفقة مقدم الطلب لبيان موقع المتجر بشكل مفصل ولوصف المتجر ومطابقته مع القيد العقاري و مخططات العقارية و بيان الشاغل و بيان عناصر المتجر المتوفرة مع اة أحكام البند /٦/ السابق ذكرها',
                                    'ai_tags' => '["تسجيل متجر", "السجل التجاري", "قيد عقاري", "قيد مالي", "التكليف الضريبي", "إحالة محكمة", "مخطط إفرازي", "كشف ميداني"]',
                                    'ai_summary' => 'يحدد المستند الأوراق والإجراءات القانونية اللازمة لتسجيل المتجر في السجل التجاري عندما يكون مقدم الطلب غير مالك للمتجر. تشمل المتطلبات تقديم طلب قانوني بإحالة من المحكمة المختصة، قيد عقاري ومالي، بيان تكليف ضريبي، ومخطط مساحي، مع توضيح آلية التعامل في حال غياب الصحيفة العقارية، بالإضافة إلى إجراءات الكشف الميداني لمطابقة مواصفات المتجر.',
                                    'has_summary' => 0,
                                    'has_versions' => 0,
                                    'version_number' => 1,
                                    'parent_id' => NULL,
                                    'created_at' => '2026-08-22 17:42:16',
                                    'updated_at' => '2026-08-22 17:42:47',
                                ),
                            ));
        
        
    }
}