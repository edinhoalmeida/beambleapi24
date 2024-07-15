@extends('webview.layout.layout')

@section('content')
<div class="container">
        <img src="{{ asset('team/imgs/app_icon.png') }}" class="wv-logo"> <br>
        <h2>Beamble Webview - test</h2>
        <form class="form-style-6" name="FormName" method="POST" action="{{ route('webview.onboarding') }}">
            <label>Select user to make a connect with Stripe (test mode):</label>
            <select name="user_id" id="user_id" required>
                <option value="">Please select</option>
                <option value="1111111111111111">error</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->email }} - ({{ $user->name }})</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
            <input type="submit" name="onboarding" value="go">
        </form>
        <div style="padding-left: 1rem;">
            <h3>React.js reference:</h3>
            <textarea rows="4">
                <?php
                $wv = '<WebView source={{ html: 
                    `<body onload="document.FormName.submit()">
                        <form name="FormName" method="POST" action="'.route('webview.onboarding').'">
                            <input type="hidden" name="user_id" value="19">
                        </form>
                    </body>`
                    }}
                                onMessage={this.onMessage}
                                onNavigationStateChange={(getUrl) => {
                                    console.log("getUrl", getUrl)
                                }}
                            />';
                ?>
                {{ $wv }}
            </textarea>
        </div>
</div>
@endsection