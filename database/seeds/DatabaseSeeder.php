<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role, App\Models\User, App\Models\Contact, App\Models\Post, App\Models\Tag, App\Models\PostTag, App\Models\Comment;
use App\Services\LoremIpsumGenerator;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$lipsum = new LoremIpsumGenerator;

		Role::create([
			'title' => 'Administrator',
			'slug' => 'admin'
		]);

		Role::create([
			'title' => 'Redactor',
			'slug' => 'redac'
		]);

		Role::create([
			'title' => 'User',
			'slug' => 'user'
		]);

		User::create([
			'username' => 'GreatAdmin',
			'email' => 'admin@la.fr',
			'password' => bcrypt('admin'),
			'seen' => true,
			'role_id' => 1,
			'confirmed' => true
		]);

		User::create([
			'username' => 'GreatRedactor',
			'email' => 'redac@la.fr',
			'password' => bcrypt('redac'),
			'seen' => true,
			'role_id' => 2,
			'valid' => true,
			'confirmed' => true
		]);

		User::create([
			'username' => 'Walker',
			'email' => 'walker@la.fr',
			'password' => bcrypt('walker'),
			'role_id' => 3,
			'confirmed' => true
		]);

		User::create([
			'username' => 'Slacker',
			'email' => 'slacker@la.fr',
			'password' => bcrypt('slacker'),
			'role_id' => 3,
			'confirmed' => true
		]);

		Contact::create([
			'name' => 'Dupont',
			'email' => 'dupont@la.fr',
			'text' => 'Lorem ipsum inceptos malesuada leo fusce tortor sociosqu semper, facilisis semper class tempus faucibus tristique duis eros, cubilia quisque habitasse aliquam fringilla orci non. Vel laoreet dolor enim justo facilisis neque accumsan, in ad venenatis hac per dictumst nulla ligula, donec mollis massa porttitor ullamcorper risus. Eu platea fringilla, habitasse.'
		]);

		Contact::create([
			'name' => 'Durand',
			'email' => 'durand@la.fr',
			'text' => ' Lorem ipsum erat non elit ultrices placerat, netus metus feugiat non conubia fusce porttitor, sociosqu diam commodo metus in. Himenaeos vitae aptent consequat luctus purus eleifend enim, sollicitudin eleifend porta malesuada ac class conubia, condimentum mauris facilisis conubia quis scelerisque. Lacinia tempus nullam felis fusce ac potenti netus ornare semper molestie, iaculis fermentum ornare curabitur tincidunt imperdiet scelerisque imperdiet euismod.'
		]);

		Contact::create([
			'name' => 'Martin',
			'email' => 'martin@la.fr',
			'text' => 'Lorem ipsum tempor netus aenean ligula habitant vehicula tempor ultrices, placerat sociosqu ultrices consectetur ullamcorper tincidunt quisque tellus, ante nostra euismod nec suspendisse sem curabitur elit. Malesuada lacus viverra sagittis sit ornare orci, augue nullam adipiscing pulvinar libero aliquam vestibulum, platea cursus pellentesque leo dui. Lectus curabitur euismod ad, erat.',
			'seen' => true
		]);

		Tag::create([
			'tag' => 'Tag1'
		]);

		Tag::create([
			'tag' => 'Tag2'
		]);

		Tag::create([
			'tag' => 'Tag3'
		]);

		Tag::create([
			'tag' => 'Tag4'
		]);

		Post::create([
			'title' => 'Post 1',
			'slug' => 'post-1', 
			'summary' => '<img alt="" src="/filemanager/userfiles/user2/mega-champignon.png" style="float:left; height:128px; width:128px" />' . $lipsum->getContent(50),
			'content' => $lipsum->getContent(500), 
			'active' => true,
			'user_id' => 1
		]);

		Post::create([
			'title' => 'Post 2',
			'slug' => 'post-2', 
			'summary' => '<img alt="" src="/filemanager/userfiles/user2/goomba.png" style="float:left; height:128px; width:128px" />' . $lipsum->getContent(50),
			'content' => '<p>Lorem ipsum convallis ac curae non elit ultrices placerat netus metus feugiat, non conubia fusce porttitor sociosqu diam commodo metus in himenaeos, vitae aptent consequat luctus purus eleifend enim sollicitudin eleifend porta. Malesuada ac class conubia condimentum mauris facilisis conubia quis scelerisque lacinia, tempus nullam felis fusce ac potenti netus ornare semper. Molestie iaculis fermentum ornare curabitur tincidunt imperdiet scelerisque, imperdiet euismod scelerisque torquent curae rhoncus, sollicitudin tortor placerat aptent hac nec. Posuere suscipit sed tortor neque urna hendrerit vehicula duis litora tristique congue nec auctor felis libero, ornare habitasse nec elit felis inceptos tellus inceptos cubilia quis mattis faucibus sem non.</p>

<p>Odio fringilla class aliquam metus ipsum lorem luctus pharetra dictum, vehicula tempus in venenatis gravida ut gravida proin orci, quis sed platea mi quisque hendrerit semper hendrerit. Facilisis ante sapien faucibus ligula commodo vestibulum rutrum pretium, varius sem aliquet himenaeos dolor cursus nunc habitasse, aliquam ut curabitur ipsum luctus ut rutrum. Odio condimentum donec suscipit molestie est etiam sit rutrum dui nostra, sem aliquet conubia nullam sollicitudin rhoncus venenatis vivamus rhoncus netus, risus tortor non mauris turpis eget integer nibh dolor. Commodo venenatis ut molestie semper adipiscing amet cras, class donec sapien malesuada auctor sapien arcu inceptos, aenean consequat metus litora mattis vivamus.</p>

<pre>
<code class="language-php">protected function getUserByRecaller($recaller)
{
	if ($this-&gt;validRecaller($recaller) &amp;&amp; ! $this-&gt;tokenRetrievalAttempted)
	{
		$this-&gt;tokenRetrievalAttempted = true;

		list($id, $token) = explode("|", $recaller, 2);

		$this-&gt;viaRemember = ! is_null($user = $this-&gt;provider-&gt;retrieveByToken($id, $token));

		return $user;
	}
}</code></pre>

<p>Feugiat arcu adipiscing mauris primis ante ullamcorper ad nisi, lobortis arcu per orci malesuada blandit metus tortor, urna turpis consectetur porttitor egestas sed eleifend. Eget tincidunt pharetra varius tincidunt morbi malesuada elementum mi torquent mollis, eu lobortis curae purus amet vivamus amet nulla torquent, nibh eu diam aliquam pretium donec aliquam tempus lacus. Tempus feugiat lectus cras non velit mollis sit et integer, egestas habitant auctor integer sem at nam massa himenaeos, netus vel dapibus nibh malesuada leo fusce tortor. Sociosqu semper facilisis semper class tempus faucibus tristique duis eros, cubilia quisque habitasse aliquam fringilla orci non vel, laoreet dolor enim justo facilisis neque accumsan in.</p>

<p>Ad venenatis hac per dictumst nulla ligula donec, mollis massa porttitor ullamcorper risus eu platea, fringilla habitasse suscipit pellentesque donec est. Habitant vehicula tempor ultrices placerat sociosqu ultrices consectetur ullamcorper tincidunt quisque tellus, ante nostra euismod nec suspendisse sem curabitur elit malesuada lacus. Viverra sagittis sit ornare orci augue nullam adipiscing pulvinar libero aliquam vestibulum platea cursus pellentesque leo dui lectus, curabitur euismod ad erat curae non elit ultrices placerat netus metus feugiat non conubia fusce porttitor. Sociosqu diam commodo metus in himenaeos vitae aptent consequat luctus purus eleifend enim sollicitudin eleifend, porta malesuada ac class conubia condimentum mauris facilisis conubia quis scelerisque lacinia.</p>

<p>Tempus nullam felis fusce ac potenti netus ornare semper molestie iaculis, fermentum ornare curabitur tincidunt imperdiet scelerisque imperdiet euismod. Scelerisque torquent curae rhoncus sollicitudin tortor placerat aptent hac, nec posuere suscipit sed tortor neque urna hendrerit, vehicula duis litora tristique congue nec auctor. Felis libero ornare habitasse nec elit felis, inceptos tellus inceptos cubilia quis mattis, faucibus sem non odio fringilla. Class aliquam metus ipsum lorem luctus pharetra dictum vehicula, tempus in venenatis gravida ut gravida proin orci, quis sed platea mi quisque hendrerit semper.</p>
', 
			'active' => true,
			'user_id' => 2
		]);

		Post::create([
			'title' => 'Post 3',
			'slug' => 'post-3', 
			'summary' => '<img alt="" src="/filemanager/userfiles/user2/rouge-shell.png" style="float:left; height:128px; width:128px" />' . $lipsum->getContent(50),
			'content' => $lipsum->getContent(500), 
			'active' => true,
			'user_id' => 2
		]);

		Post::create([
			'title' => 'Post 4',
			'slug' => 'post-4', 
			'summary' => '<img alt="" src="/filemanager/userfiles/user2/rouge-shyguy.png" style="float:left; height:128px; width:128px" />' . $lipsum->getContent(50),
			'content' => $lipsum->getContent(500), 
			'active' => true,
			'user_id' => 2
		]);

		PostTag::create([
			'post_id' => 1,
			'tag_id' => 1
		]);

		PostTag::create([
			'post_id' => 1,
			'tag_id' => 2
		]);

		PostTag::create([
			'post_id' => 2,
			'tag_id' => 1
		]);

		PostTag::create([
			'post_id' => 2,
			'tag_id' => 2
		]);

		PostTag::create([
			'post_id' => 2,
			'tag_id' => 3
		]);

		PostTag::create([
			'post_id' => 3,
			'tag_id' => 1
		]);

		PostTag::create([
			'post_id' => 3,
			'tag_id' => 2
		]);

		PostTag::create([
			'post_id' => 3,
			'tag_id' => 4
		]);

		Comment::create([
			'content' => $lipsum->getContent(200), 
			'user_id' => 2,
			'post_id' => 1
		]);

		Comment::create([
			'content' => $lipsum->getContent(200), 
			'user_id' => 2,
			'post_id' => 2
		]);

		Comment::create([
			'content' => $lipsum->getContent(200), 
			'user_id' => 3,
			'post_id' => 1
		]);

	}

}
