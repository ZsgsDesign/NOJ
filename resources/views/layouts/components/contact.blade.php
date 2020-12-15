@unless(blank(config("contact.email"))) <p class="mb-0"><i class="MDI email"></i> {{config("contact.email")}}</p> @endunless
@unless(blank(config("contact.qq"))) <p class="mb-0"><i class="MDI qqchat"></i> {{config("contact.qq")}}</p> @endunless
@unless(blank(config("contact.tel"))) <p class="mb-0"><i class="MDI phone"></i> {{config("contact.tel")}}</p> @endunless
