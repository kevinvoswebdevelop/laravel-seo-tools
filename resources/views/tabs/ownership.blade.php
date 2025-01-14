<p class="text-small text-muted">This data is shown as metadata in your site. It is intended to appear in
    Google's Knowledge Graph.
    You can be either a company or a person</p>
<form action="{{route('seo::settings.store')}}" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <div class="form-group row">
        <label for="settings_ownership_type" class="col-sm-3">Organization / person</label>
        <select class="form-control col-sm-9" name="settings[ownership_type][value]"
                id="settings_ownership_type" required>
            <option value="">Choose</option>
            <option value="Person" {{$model->getValueByKey('ownership_type')=='Person'?'selected':''}}>Person</option>
            <option value="Organization" {{$model->getValueByKey('ownership_type')=='Organization'?'selected':''}}>
                Organization
            </option>
        </select>
    </div>

    <div class="form-group row">
        <label for="settings_ownership_url" class="col-sm-3"><i class="fa fa-external-link"></i> Web
            Site</label>
        <input type="url" name="settings[ownership_url][value]" class="form-control col-sm-9"
               id="settings_ownership_url" value="{{$model->getValueByKey('ownership_url')}}"
               placeholder="e.g. www.your-site.com" required>
    </div>

    <div class="form-group row">
        <label for="settings_ownership_name" class="col-sm-3"><i class="fa fa-user-circle"></i> Name </label>
        <input type="text" name="settings[ownership_name][value]" class="form-control col-sm-9"
               id="settings_ownership_name" value="{{$model->getValueByKey('ownership_name')}}"
               placeholder="e.g. Tuhin Bepari" required>
    </div>

    <div class="form-group row">
        <label for="settings_ownership_email" class="col-sm-3"><i class="fa fa-envelope"></i> Email Address</label>
        <input type="email" name="settings[ownership_email][value]" class="form-control col-sm-9"
               id="settings_ownership_email" value="{{$model->getValueByKey('ownership_email')}}"
               placeholder="e.g. mail@your-site.com">
    </div>

    <div class="form-group row">
        <label for="settings_ownership_address" class="col-sm-3"><i class="fa fa-map-marker"></i> Address </label>
        <input type="text" name="settings[ownership_address][value]" class="form-control col-sm-9"
               id="settings_ownership_address" value="{{$model->getValueByKey('ownership_address')}}"
               placeholder="e.g. locality, city, Country">
        <p class="text-muted text-center">Physical address of Company</p>
    </div>
    <div class="form-group row">
        <label for="ownership_contact_point_telephone" class="col-sm-3"><i class="fa fa-phone-square"></i> Customer
            Service Number </label>
        <input type="text" name="settings[ownership_contact_point_telephone][value]" class="form-control col-sm-9"
               id="ownership_contact_point_telephone"
               value="{{$model->getValueByKey('ownership_contact_point_telephone')}}"
               placeholder="e.g. +1-401-555-1212">
    </div>
    <div class="form-group row">

        <label for="settings_ownership_logo" class="col-sm-3"><i class="fa fa-image"></i> Logo
        </label>
        <input type="file" id="settings_ownership_logo" name="settings[ownership_logo][value]"
               class="form-control-lg" placeholder="e.g. https://www.your-site.com/logo.png">
        <p class="text-muted text-center">
            URL of a logo that is representative of the organization. The image must be 112x112px, at minimum and in
            .jpg, .png, or. gif format
        </p>

    </div>

    @if($model->getValueByKey('ownership_logo'))
        <img src="{{$model->getValueByKey('ownership_logo')}}" width="120px">
    @endif

    <br/><br/>

    <h3>Review / Rating</h3>
    <div class="form-group row">
        <label for="settings_review_rating_value" class="col-sm-3">Average rating value </label>
        <input type="number" name="settings[review_rating_value][value]" class="form-control col-sm-9"
               id="settings_review_rating_value" value="{{$model->getValueByKey('review_rating_value')}}"
               placeholder="e.g. 10">
    </div>
    <div class="form-group row">
        <label for="settings_review_count" class="col-sm-3">Number of reviews/ratings </label>
        <input type="number" name="settings[review_count][value]" class="form-control col-sm-9"
               id="settings_review_count" value="{{$model->getValueByKey('review_count')}}"
               placeholder="e.g. 100">
    </div>
    <div class="form-group row">
        <label for="settings_review_worst_rating" class="col-sm-3">Minimal rating value </label>
        <input type="number" name="settings[review_worst_rating][value]" class="form-control col-sm-9"
               id="settings_review_worst_rating" value="{{$model->getValueByKey('review_worst_rating')}}"
               placeholder="e.g. 1">
    </div>
    <div class="form-group row">
        <label for="settings_review_best_rating" class="col-sm-3">Maximal rating value </label>
        <input type="number" name="settings[review_best_rating][value]" class="form-control col-sm-9"
               id="settings_review_best_rating" value="{{$model->getValueByKey('review_best_rating')}}"
               placeholder="e.g. 10">
    </div>

    <br/><br/>

    <h3>FAQ</h3>

    @for($i = 0; $i < 10; $i++)
        <div class="form-group row">
            <label for="settings_faq_question_{{$i}}" class="col-sm-3">FAQ Question {{$i}} </label>
            <input type="text" name="settings[faq_question_{{$i}}][value]" class="form-control col-sm-9"
                   id="settings_faq_question_{{$i}}" value="{{$model->getValueByKey('faq_question_'.$i)}}"
                   placeholder="e.g. How do I convert bitcoin to usd?">
        </div>
        <div class="form-group row">
            <label for="settings_faq_answer_{{$i}}" class="col-sm-3">FAQ Answer {{$i}} </label>
            <input type="text" name="settings[faq_answer_{{$i}}][value]" class="form-control col-sm-9"
                   id="settings_faq_answer_{{$i}}" value="{{$model->getValueByKey('faq_answer_'.$i)}}"
                   placeholder="e.g. By using our converter found on almost every page">
        </div>
    @endfor

    <div class="form-group text-right">
        <input type="submit" value="Save" class="btn btn-primary">
    </div>
</form>