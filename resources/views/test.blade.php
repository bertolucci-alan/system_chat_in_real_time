<div>
    <form action="{{route('test')}}" method="POST">
        @csrf
        <textarea name="message" id="" cols="30" rows="10" placeholder="message"></textarea>
        <button type="submit">Enviar</button>
    </form>
</div>