@extends('layouts.app')

@section('title', 'Terms of use' )

@section('content')
  <h2>Terms of Use</h2>
  <p><em>Last updated: June 2025</em></p>

  <p>These Terms of Use apply to all 3D resource packs, models, textures, and related content published under the <strong>Anime Packs</strong> project by <strong>Andrew Manusha</strong> (<a href="https://anime-packs.com" target="_blank">anime-packs.com</a>).</p>
  <p>By downloading or using any resource pack from this website, you agree to the following terms:</p>

  <h3>You Are Allowed To:</h3>
  <ul>
    <li>Use the packs for <strong>personal, non-commercial use</strong> in your Minecraft client.</li>
    <li>Showcase them in videos, screenshots, or streams, as long as you follow the credit guidelines below.</li>
    <li>Share links to the <strong>official website</strong> (<a href="https://anime-packs.com" target="_blank">anime-packs.com</a>) to recommend the packs.</li>
  </ul>

  <h3>You Are Not Allowed To:</h3>
  <ul>
    <li><strong>Modify</strong>, edit, or alter any model, texture, or file from the packs.</li>
    <li><strong>Redistribute</strong> the packs or their contents in any form, including:
      <ul>
        <li>Reuploads on third-party websites</li>
        <li>Sharing the files directly with others</li>
        <li>Including them in modpacks or public launchers</li>
      </ul>
    </li>
    <li>Use the packs in <strong>public servers or modpacks</strong> without <strong>explicit written permission</strong> and a clear revenue-sharing agreement.</li>
    <li>Claim any part of these resource packs as your own work.</li>
  </ul>

  <h3>Attribution & Sharing Guidelines</h3>
  <ul>
    <li>If you're <strong>streaming or recording</strong> gameplay with these resource packs:
      <ul>
        <li>Credit is appreciated but not required.</li>
        <li>If someone asks for the pack, you <strong>must</strong> link to <a href="https://anime-packs.com" target="_blank">anime-packs.com</a> instead of sharing the file directly.</li>
      </ul>
    </li>
    <li>If you are using the packs on a <strong>public server</strong> (even non-commercial), you <strong>must credit the author</strong>:
      <ul>
        <li>Use the name <strong>Andrew Manusha</strong> or a link to <a href="https://anime-packs.com" target="_blank">anime-packs.com</a></li>
        <li>Clearly indicate which parts of your server use these packs.</li>
      </ul>
    </li>
  </ul>

  <h3>Commissions & Custom Use</h3>
  <p>Interested in a custom-made pack? I accept commissions! Visit the <a href="/commission">Commission Page</a> for more information, or contact me directly via the website.</p>

  <p>For modpack creators, server owners, or partners: use of these packs in any commercial or public project is only permitted with written permission and a licensing agreement. Please reach out before including them in your project.</p>

  <p>&copy; {{ date('Y') }} Andrew Manusha / <a href="https://anime-packs.com" target="_blank">anime-packs.com</a>. All rights reserved.</p>
@endsection