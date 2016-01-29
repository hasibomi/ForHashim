@servers(['dev' => 'vagrant@hashim.app'])

@task('monitorStatus')
    tugboat restart ubuntu-512mb-sgp1-01
@endtask