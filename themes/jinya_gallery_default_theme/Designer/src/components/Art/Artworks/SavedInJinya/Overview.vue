<template>
    <div class="jinya-art-overview">
        <jinya-loader :loading="loading"/>
        <jinya-card-list :nothing-found="nothingFound" v-if="!loading">
            <jinya-card :header="artwork.name" v-for="artwork in artworks" v-if="!loading" :key="artwork.slug">
                <img class="jinya-art-picture" :src="artwork.picture"/>
                <jinya-card-button :to="{name: detailsRoute, params: {slug: artwork.slug}}" slot="footer" icon="monitor"
                                   type="details"/>
                <jinya-card-button :to="{name: editRoute, params: {slug: artwork.slug}}" slot="footer" icon="pencil"
                                   type="edit"/>
                <!--suppress JSUnnecessarySemicolon -->
                <jinya-card-button @click="showDeleteModal(artwork)" slot="footer" icon="delete" type="delete"/>
            </jinya-card>
        </jinya-card-list>
        <jinya-pager @previous="load(control.previous)" @next="load(control.next)" v-if="!loading" :offset="offset"
                     :count="count"/>
        <jinya-modal @close="closeDeleteModal()" title="art.artworks.delete.title" v-if="this.delete.show"
                     :loading="this.delete.loading">
            <jinya-message :message="this.delete.error" state="error" v-if="this.delete.error && !this.delete.loading"
                           slot="message"/>
            {{'art.artworks.delete.content'|jmessage({artwork: selectedArtwork.name})}}
            <jinya-modal-button :is-secondary="true" slot="buttons-left" label="art.artworks.delete.no"
                                :closes-modal="true" :is-disabled="this.delete.loading"/>
            <jinya-modal-button :is-danger="true" slot="buttons-right" label="art.artworks.delete.yes" @click="remove"
                                :is-disabled="this.delete.loading"/>
        </jinya-modal>
        <jinya-floating-action-button v-if="!loading" :is-primary="true" icon="plus" :to="addRoute"/>
    </div>
</template>

<script>
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import JinyaCardList from "@/framework/Markup/Listing/Card/CardList";
  import JinyaCard from "@/framework/Markup/Listing/Card/Card";
  import JinyaPager from "@/framework/Markup/Listing/Pager";
  import JinyaCardButton from "@/framework/Markup/Listing/Card/CardButton";
  import JinyaModal from "@/framework/Markup/Modal/Modal";
  import JinyaModalButton from "@/framework/Markup/Modal/ModalButton";
  import Translator from "@/framework/i18n/Translator";
  import JinyaMessage from "@/framework/Markup/Validation/Message";
  import JinyaLoader from "@/framework/Markup/Waiting/Loader";
  import EventBus from "@/framework/Events/EventBus";
  import Events from "@/framework/Events/Events";
  import Routes from "@/router/Routes";
  import JinyaFloatingActionButton from "@/framework/Markup/FloatingActionButton";

  export default {
    components: {
      JinyaFloatingActionButton,
      JinyaLoader,
      JinyaMessage,
      JinyaModalButton,
      JinyaModal,
      JinyaCardButton,
      JinyaPager,
      JinyaCard,
      JinyaCardList
    },
    name: "jinya-artworks-saved-in-jinya-overview",
    computed: {
      addRoute() {
        return Routes.Art.Artworks.SavedInJinya.Add;
      }
    },
    methods: {
      load(target) {
        const url = new URL(target, location.href);

        this.$router.push({
          name: Routes.Art.Artworks.SavedInJinya.Overview.name,
          query: {
            offset: url.searchParams.get('offset'),
            count: url.searchParams.get('count'),
            keyword: url.searchParams.get('keyword')
          }
        });
      },
      async fetchArtworks(offset = 0, count = 10, keyword = '') {
        this.loading = true;
        this.currentUrl = `/api/artwork?offset=${offset}&count=${count}&keyword=${keyword}`;

        const value = await JinyaRequest.get(this.currentUrl);
        this.artworks = value.items;
        this.control = value.control;
        this.count = value.count;
        this.offset = value.offset;
        this.loading = false;
      },
      selectArtwork(artwork) {
        this.selectedArtwork = artwork;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/artwork/${this.selectedArtwork.slug}`);
          this.delete.show = false;
          const url = new URL(location.href);
          await this.fetchArtworks(0, 10, url.searchParams.get('keyword'));
        } catch (reason) {
          this.delete.error = Translator.validator(`art.artworks.overview.delete.${reason.message}`);
        }
        this.delete.loading = false;
      },
      showDeleteModal(artwork) {
        this.selectArtwork(artwork);
        this.delete.show = true;
      },
      closeDeleteModal() {
        this.delete.show = false;
        this.delete.loading = false;
        this.delete.error = '';
      }
    },
    async mounted() {
      const offset = this.$route.query.offset || 0;
      const count = this.$route.query.count || 10;
      const keyword = this.$route.query.keyword || '';
      await this.fetchArtworks(offset, count, keyword);

      EventBus.$on(Events.search.triggered, value => {
        this.$router.push({
          name: Routes.Art.Artworks.SavedInJinya.Overview.name,
          query: {
            offset: 0,
            count: this.$route.query.count,
            keyword: value.keyword
          }
        });
      });
    },
    beforeDestroy() {
      EventBus.$off(Events.search.triggered);
    },
    async beforeRouteUpdate(to, from, next) {
      await this.fetchArtworks(to.query.offset || 0, to.query.count || 10, to.query.keyword || '');
      next();
    },
    data() {
      return {
        artworks: [],
        control: {next: false, previous: false},
        count: 0,
        offset: 0,
        loading: true,
        keyword: '',
        selectedArtwork: {},
        delete: {
          error: '',
          show: false,
          loading: false
        },
        editRoute: Routes.Art.Artworks.SavedInJinya.Edit.name,
        detailsRoute: Routes.Art.Artworks.SavedInJinya.Details.name,
        nothingFound: this.$route.query.keyword ? 'art.artworks.overview.nothing_found' : 'art.artworks.overview.no_artworks'
      };
    }
  }
</script>

<style scoped lang="scss">
    .jinya-art-picture {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s;

        &:hover {
            object-fit: scale-down;
        }
    }
</style>