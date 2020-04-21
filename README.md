# Laravel 7 SaaS Demo based on Envoyer Pricing

Landing Page screenshot:

![Laravel Envoyer Plans Landing screenshot](https://quickadminpanel.com/blog/wp-content/uploads/2020/04/Screen-Shot-2020-04-21-at-9.18.25-AM.png)

---

Plans Choice and Checkout screenshots

![Laravel Envoyer Plans screenshot plans](https://quickadminpanel.com/blog/wp-content/uploads/2020/04/Screen-Shot-2020-04-21-at-9.17.43-AM.png)

![Laravel Envoyer Plans screenshot checkout](https://quickadminpanel.com/blog/wp-content/uploads/2020/04/Screen-Shot-2020-04-21-at-9.18.13-AM.png)

---

Partly generated with Laravel generator: [QuickAdminPanel.com](https://quickadminpanel.com)


## How to use

- Clone the repository with __git clone__
- Copy __.env.example__ file to __.env__ and edit database credentials there
- Run __composer install__
- Run __php artisan key:generate__
- Run __php artisan migrate --seed__ (it has some seeded data for your testing)
- Fill in `.env` with your Stripe credentials
- Add your Stripe plan IDs in `roles` DB table directly or in Seed file
- That's it: launch the main URL. 
- You can click Login to enter with admin credentials __admin@admin.com__ - __password__
- Or you can click Register / TRY NOW - you will be assigned a Free Plan until you choose the plan to upgrade


---

## License

Basically, feel free to use and re-use any way you want.

---

## More from our LaravelDaily Team

- Check out our adminpanel generator [QuickAdminPanel](https://quickadminpanel.com)
- Subscribe to our [YouTube channel Laravel Business](https://www.youtube.com/channel/UCTuplgOBi6tJIlesIboymGA)
- Enroll in our [Laravel Online Courses](https://laraveldaily.teachable.com/)
