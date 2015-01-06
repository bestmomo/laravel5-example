          @foreach ($posts as $post)
            <tr {!! !$post->vu && Session::get('statut') == 'admin'? 'class="warning"' : '' !!}>
              <td class="text-primary"><strong>{{ $post->titre }}</strong></td>
              <td>{{ $post->created_at }}</td> 
              <td>{!! Form::checkbox('actif', $post->id, $post->actif) !!}</td>
              @if(Session::get('statut') == 'admin')
                <td>{{ $post->username }}</td>
                <td>{!! Form::checkbox('vu', $post->id, $post->vu) !!}</td>
              @endif
              <td>{!! link_to('blog/' . $post->slug, trans('back/blog.see'), ['class' => 'btn btn-success btn-block btn']) !!}</td>
              <td>{!! link_to_route('blog.edit', trans('back/blog.edit'), [$post->id], ['class' => 'btn btn-warning btn-block']) !!}</td>
              <td>
                {!! Form::open(['method' => 'DELETE', 'route' => ['blog.destroy', $post->id]]) !!}
                  {!! Form::destroy(trans('back/blog.destroy'), trans('back/blog.destroy-warning')) !!}
                {!! Form::close() !!}
              </td>
            </tr>
          @endforeach