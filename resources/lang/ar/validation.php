<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other يساوي :value.',
    'confirmed' => ' حقل تأكيد :attribute مطلوب.',

    'string' => 'حقل :attribute يجب أن يكون نصًا.',
    'max' => [
        'string' => 'لا يمكن أن يزيد حقل :attribute عن :max حرف.',
        'file' => 'حجم الملف :attribute يجب ألا يتجاوز :max كيلوبايت.',
    ],
    'min' => [
        'string' => 'حقل :attribute يجب أن يحتوي على :min أحرف على الأقل.',
    ],
    'unique' => 'قيمة :attribute مستخدمة بالفعل.',
    'boolean' => 'حقل :attribute يجب أن يكون إما صحيح أو خطأ.',
    'image' => 'حقل :attribute يجب أن يكون صورة.',
    'mimes' => 'حقل :attribute يجب أن يكون من النوع :values.',
    'email' => 'حقل :attribute يجب أن يكون بريدًا إلكترونيًا صالحًا.',



    // أسماء الحقول (attributes)
    'attributes' => [
        'full_name' => 'الاسم الكامل',
        'username' => 'اسم المستخدم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'roles' => 'الأدوار',
        'account_status' => 'حالة الحساب',
        'ban_status' => 'حالة الحظر',
        'image' => 'الصورة',
        'items' => 'عناصر الفاتورة',
        'invoice_date' => 'تاريخ الفاتورة',
        'customer_id' => 'الزبائن',
        'invoiceItem' => 'ادوية الفاتورة',
        'type' => 'النوع',
        'sale' => 'فاتورة بيع',
        'purchase' => 'فاتورة شراء',
        'title' => 'العنوان',

        'name' => 'الاسم /العنوان',
        'description' => 'الوصف',
        'priority' => 'الأولوية',
        'user_id' => 'المستخدم',
        'start_time' => 'وقت البدء',
        'end_time' => 'وقت الانتهاء',
        'start_date' => 'تاريخ البدء',
        'end_date' => 'تاريخ الانتهاء',
        'status' => 'الحالة',

    ],
];
